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
        if ($this->database->single("SELECT COUNT(*) FROM my_blog WHERE postID = :post_id LIMIT 1", ["post_id" => $filter_id]) > 0) {
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
            return $this->database->query("SELECT * FROM my_blog WHERE postPOSTED = '1' AND postSTATUS = 'publish' ORDER BY postDATE DESC LIMIT $max");
        } else {
            return $this->database->query("SELECT * FROM my_blog WHERE postPOSTED = '1' AND postSTATUS = 'publish' ORDER BY postDATE DESC");
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
            return $this->database->query("SELECT * FROM my_blog WHERE postCATEGORY = :cat AND postPOSTED = '1' AND postSTATUS = 'publish' ORDER BY postDATE DESC LIMIT $max", ['cat' => $category]);
        } else {
            return $this->database->query("SELECT * FROM my_blog WHERE postCATEGORY = :cat AND postPOSTED = '1' AND postSTATUS = 'publish' ORDER BY postDATE DESC", ['cat' => $category]);
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
            return $this->database->query("SELECT * FROM my_blog WHERE postAUTHOR = :authorid AND postPOSTED = '1' AND postSTATUS = 'publish' ORDER BY postDATE DESC LIMIT $max", ['authorid' => $authorId]);
        } else {
            return $this->database->query("SELECT * FROM my_blog WHERE postAUTHOR = :authorid AND postPOSTED = '1' AND postSTATUS = 'publish' ORDER BY postDATE DESC", ['authorid' => $authorId]);
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
            $titleSQL[] = "if (postTITLE LIKE '%{$words}%', {$scoreFullTitle}, 0)";
            $docSQL[] = "if (postCONT LIKE '%{$words}%', {$scoreFullDocument}, 0)";
        }

        foreach ($keywords as $key) {
            $titleSQL[] = "if (postTITLE LIKE '%{$key}%', {$scoreTitleKeyword}, 0)";
            $docSQL[] = "if (postCONT LIKE '%{$key}%', {$scoreDocumentKeyword}, 0)";
            $urlSQL[] = "if (postPERMALINK LIKE '%{$key}%', {$scoreUrlKeyword}, 0)";
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
            WHERE postPOSTED = '1' AND postSTATUS = 'publish'
            HAVING relevance > 0
            ORDER BY relevance DESC, postDATE DESC
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
            WHERE postPOSTED = '1' AND postSTATUS = 'publish'
            HAVING relevance > 0
            ORDER BY relevance DESC, postDATE DESC";

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
            $number = $this->database->rowCount("SELECT * FROM my_blog WHERE postCATEGORY = :postCATEGORY AND postPOSTED = '1' AND postSTATUS = 'publish' ", ["postCATEGORY" => $category['catNAME']]);

            if ($number > 0) {
                $list[] = ["catNAME" => $category['catNAME'], "score" => $number];
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
        //return $this->database->query("SELECT *, count(*) AS count FROM my_blog INNER JOIN my_blog_category ON (my_blog.postCATEGORY = my_blog_category.catNAME) GROUP BY my_blog_category.catNAME ORDER BY count(*) DESC LIMIT 10");
        //return $this->database->query("SELECT * FROM my_blog b, my_blog_category c WHERE b.postCATEGORY = c.catNAME");
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
                $information = $this->database->single("SELECT postID FROM my_blog WHERE postID = :blog_id LIMIT 1", ['blog_id' => $id]);
                break;
            case 'titleOriginal':
                $information = $this->database->single("SELECT postTITLE FROM my_blog WHERE postID = :blog_id LIMIT 1", ['blog_id' => $id]);
                $information = htmlspecialchars($information);
                break;
            case 'title':
                $information = $this->database->single("SELECT postTITLE FROM my_blog WHERE postID = :blog_id LIMIT 1", ['blog_id' => $id]);
                $information = htmlspecialchars($information);
                break;
            case 'content':
                $information = $this->database->single("SELECT postCONT FROM my_blog WHERE postID = :blog_id LIMIT 1", ['blog_id' => $id]);
                break;
            case 'date':
                $information = $this->database->single("SELECT postDATE FROM my_blog WHERE postID = :blog_id LIMIT 1", ['blog_id' => $id]);
                break;
            case 'authorID':
                $information = $this->database->single("SELECT postAUTHOR FROM my_blog WHERE postID = :blog_id LIMIT 1", ['blog_id' => $id]);
                break;
            case 'authorName':
                $authorID = $this->database->single("SELECT postAUTHOR FROM my_blog WHERE postID = :blog_id LIMIT 1", ['blog_id' => $id]);
                $information = $this->container['users']->getInfo($authorID, 'name') . ' ' . $this->container['users']->getInfo($authorID, 'surname');
                break;
            case 'category':
                $information = $this->database->single("SELECT postCATEGORY FROM my_blog WHERE postID = :blog_id LIMIT 1", ['blog_id' => $id]);
                break;
            case 'permalink':
                $information = $this->database->single("SELECT postPERMALINK FROM my_blog WHERE postID = :blog_id LIMIT 1", ['blog_id' => $id]);
                break;
            case 'idFROMpermalink':
                $information = $this->database->single("SELECT postID FROM my_blog WHERE postPERMALINK = :perm_id LIMIT 1", ['perm_id' => $id]);
                break;
            case 'commentsNumber':
                $information = $this->database->rowCount("SELECT * FROM my_blog_post_comments WHERE postid = :blog_id AND enable = '1' LIMIT 1", ['blog_id' => $id]);
                break;
            case 'comments':
                $information = $this->database->query("SELECT * FROM my_blog_post_comments WHERE postid = :blog_id AND enable = '1'", ['blog_id' => $id]);
                break;
            case 'postSTATUS':
                $information = $this->database->single("SELECT postSTATUS FROM my_blog WHERE postID = :perm_id LIMIT 1", ['perm_id' => $id]);
                break;
            case 'commentSTATUS':
                $information = $this->database->single("SELECT commentSTATUS FROM my_blog WHERE postPERMALINK = :perm_id LIMIT 1", ['perm_id' => $id]);
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
     * Deprecated (only for compatibility issues)
     *
     * @param $object
     * @param $id
     * @return null
     */
    function get($object, $id)
    {
        if (empty($id)) {
            return null;
        }

        if (empty($object)) {
            return null;
        } else {
            switch ($object) {
                case 'id':
                    $informazione = $this->database->single("SELECT postID FROM my_blog WHERE postID = :blog_id LIMIT 1", ['blog_id' => $id]);
                    echo $informazione;
                    break;
                case 'title':
                    $informazione = $this->database->single("SELECT postTITLE FROM my_blog WHERE postID = :blog_id LIMIT 1", ['blog_id' => $id]);
                    echo htmlspecialchars($informazione);
                    break;
                case 'content':
                    $informazione = $this->database->single("SELECT postCONT FROM my_blog WHERE postID = :blog_id LIMIT 1", ['blog_id' => $id]);
                    echo $informazione;
                    break;
                case 'date':
                    $informazione = $this->database->single("SELECT postDATE FROM my_blog WHERE postID = :blog_id LIMIT 1", ['blog_id' => $id]);
                    echo $informazione;
                    break;
                case 'author':
                    $informazione = $this->database->single("SELECT postAUTHOR FROM my_blog WHERE postID = :blog_id LIMIT 1", ['blog_id' => $id]);
                    echo removeSpace($informazione);
                    break;
                case 'authorspace':
                    $informazione = $this->database->single("SELECT postAUTHOR FROM my_blog WHERE postID = :blog_id LIMIT 1", ['blog_id' => $id]);
                    echo $informazione;
                    break;
                case 'category':
                    $informazione = $this->database->single("SELECT postCATEGORY FROM my_blog WHERE postID = :blog_id LIMIT 1", ['blog_id' => $id]);
                    echo $informazione;
                    break;
                case 'permalink':
                    $informazione = $this->database->single("SELECT postPERMALINK FROM my_blog WHERE postID = :blog_id LIMIT 1", ['blog_id' => $id]);
                    echo $informazione;
                    break;
                case 'idFROMpermalink':
                    $informazione = $this->database->single("SELECT postID FROM my_blog WHERE postPERMALINK = :perm_id LIMIT 1", ['perm_id' => $id]);
                    echo $informazione;
                    break;
            }
        }
    }

    /**
     * Deprecated (only for compatibility issues)
     *
     * @param $object
     * @param $id
     * @return null
     */
    function gets($object, $id)
    {
        if (empty($id)) {
            return null;
        }

        if (empty($object)) {
            return null;
        } else {
            switch ($object) {
                case 'id':
                    $informazione = $this->database->single("SELECT postID FROM my_blog WHERE postID = :blog_id LIMIT 1", ['blog_id' => $id]);

                    return $informazione;
                    break;
                case 'title':
                    $informazione = $this->database->single("SELECT postTITLE FROM my_blog WHERE postID = :blog_id LIMIT 1", ['blog_id' => $id]);

                    return htmlspecialchars($informazione);
                    break;
                case 'content':
                    $informazione = $this->database->single("SELECT postCONT FROM my_blog WHERE postID = :blog_id LIMIT 1", ['blog_id' => $id]);

                    return $informazione;
                    break;
                case 'date':
                    $informazione = $this->database->single("SELECT postDATE FROM my_blog WHERE postID = :blog_id LIMIT 1", ['blog_id' => $id]);

                    return $informazione;
                    break;
                case 'author':
                    $informazione = $this->database->single("SELECT postAUTHOR FROM my_blog WHERE postID = :blog_id LIMIT 1", ['blog_id' => $id]);

                    return removeSpace($informazione);
                    break;
                case 'authorspace':
                    $informazione = $this->database->single("SELECT postAUTHOR FROM my_blog WHERE postID = :blog_id LIMIT 1", ['blog_id' => $id]);

                    return $informazione;
                    break;
                case 'category':
                    $informazione = $this->database->single("SELECT postCATEGORY FROM my_blog WHERE postID = :blog_id LIMIT 1", ['blog_id' => $id]);

                    return $informazione;
                    break;
                case 'permalink':
                    $informazione = $this->database->single("SELECT postPERMALINK FROM my_blog WHERE postID = :blog_id LIMIT 1", ['blog_id' => $id]);

                    return $informazione;
                    break;
                case 'idFROMpermalink':
                    $informazione = $this->database->single("SELECT postID FROM my_blog WHERE postPERMALINK = :perm_id LIMIT 1", ['perm_id' => $id]);

                    return $informazione;
                    break;
            }
        }
    }

    /**
     * @param null $permalink
     * @return null
     */
    function permalinkFinder($permalink = null)
    {
        if (!empty($permalink)) {
            $sql = $this->database->iftrue("SELECT * FROM my_blog WHERE postPERMALINK = :permalink LIMIT 1", ['permalink' => $permalink]);

            return $sql;
        } else {
            return null;
        }
    }

    /**
     * @param null $category
     * @return null
     */
    function categoryFinder($category = null)
    {
        if (!empty($category)) {
            $sql = $this->database->iftrue("SELECT * FROM my_blog_category WHERE catNAME = :category LIMIT 1", ['category' => $category]);

            return $sql;

        } else {
            return null;
        }
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
}
