<?php
define('MY_CMS_PATH', true);
define("LOADER_LOAD_PAGE", false);
include '../../../../src/Bootstrap.php';

if ($app->container['users']->userLoggedIn()) {
    if(!$app->container['users']->currentUserHasPermission("edit_pages"))
        return;

    if (isset($_POST['content']) && isset($_POST['pageId'])) {
        $content = addslashes(base64_decode($_POST['content']));

        if (is_numeric($_POST['pageId'])) {
            if ($app->container['database']->single("SELECT count(*) FROM my_page WHERE pageId = '" . $_POST['pageId'] . "' LIMIT 1") > 0) {
                $pageid = $app->container['security']->mySqlSecure($_POST['pageId']);
                $app->container['database']->query("UPDATE my_page SET pageHtml = '$content' WHERE pageId = '" . $pageid . "'");
            }
        }
    }
}
?>