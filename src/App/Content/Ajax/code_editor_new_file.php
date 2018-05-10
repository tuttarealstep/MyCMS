<?php
/**
 * User: tuttarealstep
 */
define('MY_CMS_PATH', true);
define("LOADER_LOAD_PAGE", false);
include '../../../../src/Bootstrap.php';

$app->container['users']->hideIfNotLogged();
if ($app->container['users']->userLoggedIn()) {
    if(!$app->container['users']->currentUserHasPermission("edit_files")) {
        return;
    }

    if (isset($_POST['file_name']) && isset($_POST['theme_v'])) {
        $file_name = base64_decode($_POST['file_name']);
        $theme_var = base64_decode($_POST['theme_v']);

        $file_name = str_replace("../", "", $file_name);
        $file_name = str_replace("./", "", $file_name);

        if (file_exists("../../../../src/App/Content/Theme/" . $theme_var)) {
            if (!file_exists(dirname("../../../../src/App/Content/Theme/" . $theme_var . "/" . $file_name))) {
                mkdir(dirname("../../../../src/App/Content/Theme/" . $theme_var . "/" . $file_name), 0777, true);
            }
            $newFile = fopen("../../../../src/App/Content/Theme/" . $theme_var . "/" . $file_name, "w");
            fclose($newFile);
        }
    }
}
