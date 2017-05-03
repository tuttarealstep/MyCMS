<?php
define('MY_CMS_PATH', true);
define("LOADER_LOAD_PAGE", false);
include '../../../../src/Bootstrap.php';

if ($app->container['users']->staffLoggedIn()) {
    if (isset($_POST['content']) && isset($_POST['pageID'])) {
        $content = addslashes(base64_decode($_POST['content']));

        if (is_numeric($_POST['pageID'])) {
            if ($app->container['database']->single("SELECT count(*) FROM my_page WHERE pageID = '" . $_POST['pageID'] . "' LIMIT 1") > 0) {
                $pageid = $app->container['security']->mySqlSecure($_POST['pageID']);
                $app->container['database']->query("UPDATE my_page SET pageHTML = '$content' WHERE pageID = '" . $pageid . "'");
            }
        }
    }
}
?>