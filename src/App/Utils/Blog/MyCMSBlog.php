<?php
/**
 * User: tuttarealstep
 * Date: 10/04/16
 * Time: 0.36
 */

namespace MyCMS\App\Utils\Blog;

class MyCMSBlog
{
    private $database, $settings, $container;

    /**
     * MyCMSBlog constructor.
     * @param $container
     */
    function __construct($container)
    {
        $this->database = $container["database"];
        $this->settings = $container["settings"];
        $this->container = $container;
    }

    /**
     * Check if post exist
     * @param $postId
     * @return bool
     */
    public function verifyPostId($postId)
    {
        $filter_id = filter_var($postId, FILTER_SANITIZE_NUMBER_INT);
        if ($this->database->single("SELECT COUNT(*) FROM my_blog WHERE postId = :post_id LIMIT 1", ["post_id" => $filter_id]) > 0) {
            return true;
        }

        return false;
    }

    /**
     * Return all or $max post (return $max posts if max > -1)
     * @param int $max
     * @return mixed
     */
    public function getPosts($max = -1)
    {
        if ($max > -1) {
            return $this->database->query("SELECT * FROM my_blog WHERE postStatus = 'publish' ORDER BY postDate DESC LIMIT $max");
        } else {
            return $this->database->query("SELECT * FROM my_blog WHERE postStatus = 'publish' ORDER BY postDate DESC");
        }
    }

    /**
     * Return all or $max post from a category (return $max posts if max > -1)
     * @param int $max
     * @param $category
     * @return mixed
     */
    public function getPostsFromCategory($max = -1, $category)
    {
        if ($max > -1) {
            return $this->database->query("SELECT my_blog.* FROM my_blog, my_blog_category, my_blog_category_relationships WHERE my_blog_category_relationships.categoryId = my_blog_category.categoryId AND my_blog_category.categoryName = :cat AND my_blog.postStatus = 'publish' AND my_blog.postId = my_blog_category_relationships.postId ORDER BY postDate DESC LIMIT $max", ['cat' => $category]);
        } else {
            return $this->database->query("SELECT my_blog.* FROM my_blog, my_blog_category, my_blog_category_relationships WHERE my_blog_category_relationships.categoryId = my_blog_category.categoryId AND my_blog_category.categoryName = :cat AND my_blog.postStatus = 'publish' AND my_blog.postId = my_blog_category_relationships.postId ORDER BY postDate DESC", ['cat' => $category]);
        }
    }

    /**
     * Return all or $max post from an author id (return $max posts if max > -1)
     * @param int $max
     * @param $authorId
     * @return mixed
     */
    public function getPostsFromAuthorId($max = -1, $authorId)
    {
        if ($max > -1) {
            return $this->database->query("SELECT * FROM my_blog WHERE postAuthor = :authorid AND postStatus = 'publish' ORDER BY postDate DESC LIMIT $max", ['authorid' => $authorId]);
        } else {
            return $this->database->query("SELECT * FROM my_blog WHERE postAuthor = :authorid AND postStatus = 'publish' ORDER BY postDate DESC", ['authorid' => $authorId]);
        }
    }

    /**
     * $max if -1 all post, if > i where i > 0 return i post.
     * @param int $max
     * @param $words
     * @return mixed
     */
    public function getPostsFromSearch($max = -1, $words)
    {
        $scoreFullTitle = 6;
        $scoreTitleKeyword = 5;
        $scoreDocumentKeyword = 4;
        $scoreFullDocument = 4;
        $scoreUrlKeyword = 1;

        $keywords = $this->filterSearch($words);

        $titleSQL = [];
        $docSQL = [];
        $urlSQL = [];

        if (count($keywords) > 0) {
            $titleSQL[] = "if (postTitle LIKE '%{$words}%', {$scoreFullTitle}, 0)";
            $docSQL[] = "if (postTitle LIKE '%{$words}%', {$scoreFullDocument}, 0)";
        }

        foreach ($keywords as $key) {
            $titleSQL[] = "if (postTitle LIKE '%{$key}%', {$scoreTitleKeyword}, 0)";
            $docSQL[] = "if (postContent LIKE '%{$key}%', {$scoreDocumentKeyword}, 0)";
            $urlSQL[] = "if (postName LIKE '%{$key}%', {$scoreUrlKeyword}, 0)";
        }

        if (empty($titleSQL)) {
            $titleSQL[] = 0;
        }
        if (empty($docSQL)) {
            $docSQL[] = 0;
        }
        if (empty($urlSQL)) {
            $urlSQL[] = 0;
        }

        if ($max > -1) {
            $sql = "SELECT *,
            (
                (-- Title score
                " . implode(" + ", $titleSQL) . "
                )+
                (-- document
                " . implode(" + ", $docSQL) . "
                )+
                (-- url
                " . implode(" + ", $urlSQL) . "
                )
            ) as relevance
            FROM my_blog
            WHERE postStatus = 'publish'
            HAVING relevance > 0
            ORDER BY relevance DESC, postDate DESC
            LIMIT {$max}";

            return $this->database->query($sql);
        } else {
            $sql = "SELECT *,
            (
                (-- Title score
                " . implode(" + ", $titleSQL) . "
                )+
                (-- document
                " . implode(" + ", $docSQL) . "
                )+
                (-- url
                " . implode(" + ", $urlSQL) . "
                )
            ) as relevance
            FROM my_blog
            WHERE postStatus = 'publish'
            HAVING relevance > 0
            ORDER BY relevance DESC, postDate DESC";

            return $this->database->query($sql);
        }
    }

    public function filterSearch($string)
    {
        $string = urldecode($string);
        $query = trim(preg_replace("/(\\s+)+/", " ", $string));
        $keywords = [];

        $cont = 0;

        foreach (explode(" ", $query) as $key) {
            $keywords[] = $key;
            if ($cont >= 15) {
                break;
            }
            $cont++;
        }


        foreach ($keywords as $key => $word) {
            foreach ($this->container['theme']->tag as $tag => $value) {
                if (strtolower($word) == strtolower($value)) {
                    $keywords[ $key ] = "{@{$tag}@}";
                    break;
                }
            }
        }

        return $keywords;
    }

    public function getCategories()
    {
        $categories = $this->database->query("SELECT * FROM my_blog_category");

        $list = [];

        foreach ($categories as $category) {
            $number = $this->database->rowCount("SELECT my_blog.* FROM my_blog, my_blog_category_relationships WHERE my_blog_category_relationships.postId = my_blog.postId AND my_blog_category_relationships.categoryId = :categoryId AND postStatus = 'publish' ", ["categoryId" => $category['categoryId']]);

            if ($number > 0) {
                $list[] = ["categoryName" => $category['categoryName'], "score" => $number];
            }
        }


        for ($i = count($list) - 1; $i >= 0; $i--) {
            for ($j = 0; $j < $i; $j++) {
                if ($list[ $j ]["score"] > $list[ $j + 1 ]["score"]) {
                    $backup = $list[ $j ];
                    $list[ $j ] = $list[ $j + 1 ];
                    $list[ $j + 1 ] = $backup;
                } else {
                    continue;
                }
            }
        }


        $check = true;
        $n_numeri = count($list);

        while ($check) {
            $check = false;
            for ($i = $n_numeri - 2; $i >= 0; $i--) {
                if ($list[ $i ]["score"] > $list[ $i + 1 ]["score"]) {
                    $backup = $list[ $i ];
                    $list[ $i ] = $list[ $i + 1 ];
                    $list[ $i + 1 ] = $backup;
                    $n_numeri--;
                    $check = true;
                }
            }
        }


        return $list;
    }



    /**
     * @param $type
     * @param $id
     * @param bool $return
     * @return bool|null|string
     */
    public function getInfo($type, $id, $return = true)
    {
        if (empty($id))
            return false;

        $information = null;

        switch ($type) {
            case 'id':
                $information = $id;
                break;
            case 'titleOriginal':
                $information = $this->database->single("SELECT postTitle FROM my_blog WHERE postId = :blog_id LIMIT 1", ['blog_id' => $id]);
                break;
            case 'title':
                $information = $this->database->single("SELECT postTitle FROM my_blog WHERE postId = :blog_id LIMIT 1", ['blog_id' => $id]);
                $information = htmlspecialchars_decode($information);
                break;
            case 'content':
                $information = $this->database->single("SELECT postContent FROM my_blog WHERE postId = :blog_id LIMIT 1", ['blog_id' => $id]);
                break;
            case 'date':
                $information = $this->database->single("SELECT postDate FROM my_blog WHERE postId = :blog_id LIMIT 1", ['blog_id' => $id]);
                break;
            case 'authorID':
                $information = $this->database->single("SELECT postAuthor FROM my_blog WHERE postId = :blog_id LIMIT 1", ['blog_id' => $id]);
                break;
            case 'authorName':
                $authorID = $this->database->single("SELECT postAuthor FROM my_blog WHERE postId = :blog_id LIMIT 1", ['blog_id' => $id]);
                $information = $this->container['users']->getInfo($authorID, 'name') . ' ' . $this->container['users']->getInfo($authorID, 'surname');
                break;
            case 'categoryIdArray':
                $information = $this->database->query("SELECT categoryId FROM my_blog_category_relationships WHERE postId = :blog_id", ['blog_id' => $id]);
                break;
            case 'categoryNameArray':
                $information = $this->database->column("SELECT categoryName FROM my_blog_category, my_blog_category_relationships WHERE my_blog_category.categoryId = my_blog_category_relationships.categoryId AND my_blog_category_relationships.postId = :blog_id", ['blog_id' => $id]);
                break;
            case 'permalink':
                $information = $this->database->single("SELECT postName FROM my_blog WHERE postId = :blog_id LIMIT 1", ['blog_id' => $id]);
                break;
            case 'fullPermalink':
                $information = "/blog/" . date('Y', strtotime($this->getInfo("date", $id, true))) . "/" . date('m', strtotime($this->getInfo("date",$id, true))) . "/" . $this->getInfo("permalink", $id, true);
                 break;
            case 'idFROMpermalink':
                $information = $this->database->single("SELECT postId FROM my_blog WHERE postName = :perm_id LIMIT 1", ['perm_id' => $id]);
                break;
            case 'commentsNumber':
                $information = $this->database->rowCount("SELECT * FROM my_blog_post_comments WHERE postid = :blog_id AND enable = '1' LIMIT 1", ['blog_id' => $id]);
                break;
            case 'comments':
                $information = $this->database->query("SELECT * FROM my_blog_post_comments WHERE postid = :blog_id AND enable = '1'", ['blog_id' => $id]);
                break;
            case 'postStatus':
                $information = $this->database->single("SELECT postStatus FROM my_blog WHERE postId = :postId LIMIT 1", ['postId' => $id]);
                break;
            case 'commentStatus':
                $information = $this->database->single("SELECT commentStatus FROM my_blog WHERE postId = :postId LIMIT 1", ['postId' => $id]);
                break;
            case 'name':
                $information = $this->database->single("SELECT postName FROM my_blog WHERE postId = :postId LIMIT 1", ['postId' => $id]);
                break;
            default:
                return false;
        }

        if ($return) {
            return $information;
        } else {
            echo $information;
        }

        return false;
    }

    /**
     * @param null $permalink
     * @return null
     */
    function permalinkFinder($permalink = null)
    {
        if (!empty($permalink))
        {
            return $this->database->iftrue("SELECT * FROM my_blog WHERE postName = :permalink LIMIT 1", ['permalink' => $permalink]);
        } else {
            return false;
        }
    }

    /**
     * @param null $category
     * @return null
     */
    function categoryFinder($category = null)
    {
        if (!empty($category)) {
            $sql = $this->database->iftrue("SELECT * FROM my_blog_category WHERE categoryName = :category LIMIT 1", ['category' => $category]);

            return $sql;

        } else {
            return null;
        }
    }

    function getCategoryId($categoryName)
    {
        return $this->database->single("SELECT categoryId FROM my_blog_category WHERE categoryName = :categoryName LIMIT 1", ['categoryName' => $categoryName]);
    }

    /**
     * @param $postId
     * @param $comment
     */
    function addcomments($postId, $comment)
    {
        if ($this->settings->getSettingsValue('blog_comments_active') == 'true') {
            $data = $this->container['functions']->timeNormalFull(time());
            $author = $_SESSION['user']['id'];
            if (!empty($postId)) {
                if (!empty($comment)) {
                    if ($this->settings->getSettingsValue('blog_comments_approve') == 'false') {
                        $this->database->query("INSERT INTO my_blog_post_comments (author,comments,postid,date,enable) VALUES(:autore, :commento, :postid, :data, '1')", ['autore' => $author, 'commento' => $comment, 'postid' => $postId, 'data' => $data]);
                    } else {
                        $this->database->query("INSERT INTO my_blog_post_comments (author,comments,postid,date,enable) VALUES(:autore, :commento, :postid, :data,  '0')", ['autore' => $author, 'commento' => $comment, 'postid' => $postId, 'data' => $data]);
                    }
                }
            }
        }

    }

    /**
     * Set the blog page to private
     * @param $bool
     * @return bool
     */
    function setPrivate($bool)
    {
        $sql = $this->database->single("SELECT * FROM my_menu WHERE menu_name = 'Blog' LIMIT 1");
        $information = $sql;

        if ($bool == true) {
            if ($information) {
                $this->database->query("UPDATE my_menu SET menu_enabled='0' WHERE menu_name='Blog'");

                return true;
            } else {
                return false;
            }
        } else {
            if ($information) {
                $this->database->query("UPDATE my_menu SET menu_enabled='1' WHERE menu_name='Blog'");

                return true;
            } else {
                return false;
            }
        }
    }

    function generateUniqueName($name, $postId)
    {
        $name = preg_replace('~[^\pL\d]+~u', '-', $name);
        $name = iconv('utf-8', 'us-ascii//TRANSLIT', $name);
        $name = preg_replace('~[^-\w]+~', '', $name);
        $name = trim($name, '-');
        $name = preg_replace('~-+~', '-', $name);
        $name = strtolower($name);

        if(empty($name))
            $name = $postId;

        $finder = $this->permalinkFinder($name);
        if ($finder == true)
        {
            $i = 1;
            while ($this->permalinkFinder($name . '_' . $i) == true):
                $i++;
            endwhile;
            $name = $name . '_' . $i;
        }

        return $name;
    }

    function initBlogTags()
    {
        //todo add tags with media
       /* $this->container['theme']->addCallBackTag('showMedia', function ($arg, $container) {
            if(isset($arg['mediaId']))
            {
                $result = $this->container['media']->getMedia((int)$arg['mediaId']);
                if(!$result)
                    return "";

                return "asdasdsadsadassa " . $result['name'];
            }
        });*/
    }
}
