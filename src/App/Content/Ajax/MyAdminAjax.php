<?php
define('MY_CMS_PATH', true);
define("LOADER_LOAD_PAGE", false);
include '../../../../src/Bootstrap.php';

if ($app->container['users']->staffLoggedIn()) {
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
                    $app->container['users']->setInfo($_SESSION['staff']['id'], "adminColor", $color);
                }
                break;
            case 'savePostDraft':
                if(!isset($_POST['id']) && !empty($_POST['id']))
                    return;

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
}
?>