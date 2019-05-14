<?php
define('MY_CMS_PATH', true);
define("LOADER_LOAD_PAGE", false);
include '../../../../src/Bootstrap.php';

if ($app->container['users']->userLoggedIn()) {
    function newCategory($category, $app)
    {
        $finder = $app->container['blog']->categoryFinder($category);
        if ($finder == true) {
            echo "duplicate";
        } else {
            $app->container['database']->query("INSERT INTO my_blog_category (categoryName,categoryDescription) VALUES (:name, :description)", ['name' => $category, 'description' => ""]);

            ?>
            <option value="noCategory"><?php $app->container['languages']->ta('page_posts_new_select_option_no_category'); ?></option>
            <?php
            $cat = $app->container['database']->query("SELECT * FROM my_blog_category");
            foreach ($cat as $category) {
                ?>
                <option value="<?php echo $category['categoryName']; ?>"><?php echo $category['categoryName']; ?></option>
                <?php
            }
        }
    }

    if (isset($_POST['m'])) {
        switch ($_POST['m']) {
            case 'newCategory':
                if (isset($_POST['category']) && !empty($_POST['category'])) {
                    if($app->container['users']->currentUserHasPermission("manage_categories"))
                        newCategory($app->container['security']->mySqlSecure($_POST['category']), $app);
                }
                break;
            case 'customizerKeepSession':
                $_SESSION['customizerLastAction'] = time();
                break;
            case 'checkMyPageExist':
                if (isset($_POST['pageId']) && is_numeric($_POST['pageId'])) {
                    if ($app->container['page_loader']->checkIfPageExist($_POST['pageId'])) {
                        echo "true";
                    } else {
                        echo "false";
                    }
                }
                break;
            case 'customizerThemeSessionSet':
                $_SESSION['customizerThemeSession']['theme'] = $app->container['security']->mySqlSecure($_POST['theme']);
                break;
            case 'customizerThemeSessionUnset':
                unset($_SESSION['customizerThemeSession']);
                break;
            case 'changeAdminColor':
                if (isset($_POST['color']) && !empty($_POST['color'])) {
                    $color = $app->container['security']->mySqlSecure($_POST['color']);
                    $app->container['users']->setInfo($_SESSION['user']['id'], "adminColor", $color);
                }
                break;
            case 'savePostDraft':
                if(!isset($_POST['id']) && !empty($_POST['id']))
                    return;

                if($app->container['blog']->getInfo('authorID', $_POST['id']) == $_SESSION['user']['id'] && !$app->container['users']->currentUserHasPermission("edit_posts"))
                {
                    if($app->container['blog']->getInfo('postStatus', $postId) == "publish") {
                        if (!$app->container['users']->currentUserHasPermission("edit_published_posts")) {
                            return;
                        }
                    } else {
                        if (!$app->container['users']->currentUserHasPermission("edit_private_posts")) {
                            return;
                        }
                    }
                } else {
                    if($app->container['blog']->getInfo('postStatus', $postId) == "publish") {
                        if (!$app->container['users']->currentUserHasPermission("edit_published_posts")) {
                            return;
                        }
                    } else {
                        if (!$app->container['users']->currentUserHasPermission("edit_private_posts")) {
                            return;
                        }
                    }
                }

                if (isset($_POST['title']) && !empty($_POST['title']))
               {
                   $app->container['database']->query("UPDATE my_blog SET postTitle = :postTitle WHERE postId = :postId", ["postTitle" => htmlspecialchars(stripslashes($_POST['title'])), "postId" => (int)$_POST['id']]);
               }
               if (isset($_POST['content']) && !empty($_POST['content']))
                {
                    $app->container['database']->query("UPDATE my_blog SET postContent = :postContent WHERE postId = :postId", ["postContent" => $app->container['plugins']->applyEvent('parsePostContent', $_POST['content']), "postId" => (int)$_POST['id']]);
                }
                break;
        }
    }

    if(isset($_POST['action']))
    {
        switch ($_POST['action'])
        {
            case 'query-media':

                $query = "SELECT * FROM my_media WHERE 1 = 1";

                if(isset($_POST['data']['mimeType']) && !empty($_POST['data']['mimeType']))
                {
                    switch (strtolower($_POST['data']['mimeType']))
                    {
                        case 'image':
                        case 'audio':
                        case 'video':
                            $query .= " AND mime_type LIKE '" . $_POST['data']['mimeType'] . "%'";
                            break;
                    }
                }

                if(isset($_POST['data']['search']) && !empty($_POST['data']['search']))
                {
                    $query .= " AND title LIKE '%" . $_POST['data']['search'] . "%'";
                }

                if(isset($_POST['data']['orderby']) && !empty($_POST['data']['orderby']))
                {
                    $query .= " ORDER BY " . $_POST['data']['orderby'];

                    if(isset($_POST['data']['order']) && !empty($_POST['data']['order']))
                    {
                        switch (strtolower($_POST['data']['order']))
                        {
                            case 'desc':
                            case 'asc':
                                $query .= " " . strtoupper($_POST['data']['order']);
                                break;
                        }
                    }
                }

                $result = $app->container['database']->query($query);

                header("Content-Type: application/json");

                if(!$result)
                {
                    echo json_encode([]);
                    return;
                }

                echo json_encode($result);
                break;
        }
    }
}
?>