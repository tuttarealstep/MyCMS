<?php
/**
 * User: tuttarealstep
 * Date: 01/07/16
 * Time: 17.41
 */
define('MY_CMS_PATH', true);
define("LOADER_LOAD_PAGE", false);
include '../../../../src/Bootstrap.php';

$app->container['users']->hideIfStaffNotLogged();
if ($app->container['users']->staffLoggedIn()) {
    $user_rank = $app->container['users']->getInfo($_SESSION['staff']['id'], 'rank');
    if ($user_rank < 3) {
        return;
    }

    if (isset($_POST['file_c']) && isset($_POST['file_p'])) {
        $file_info = base64_decode($_POST['file_c']);
        $file_path = base64_decode($_POST['file_p']);
        file_put_contents($file_path, $file_info);
    }
}