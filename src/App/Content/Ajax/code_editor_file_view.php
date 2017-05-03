<?php
/**
 * User: tuttarealstep
 * Date: 01/07/16
 * Time: 15.26
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

    if (isset($_POST['file'])) {
        if (file_exists($_POST['file'])) {
            $file = $_POST['file'];
            $file = file_get_contents($file);
            echo base64_encode($file);
        }
    }
}