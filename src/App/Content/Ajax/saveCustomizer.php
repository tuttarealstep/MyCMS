<?php
define('MY_CMS_PATH', true);
define("LOADER_LOAD_PAGE", false);
include '../../../../src/Bootstrap.php';

if ($app->container['users']->userLoggedIn()) {
    if($app->container['users']->currentUserHasPermission("customize"))
    {
        foreach ($app->container["themeCustomizer"]->saveCallBackVector as $key => $value) {
            //echo $key . " " . base64_decode($_GET[$key]) . "<br>";
            if (isset($_GET[ $key ])) {
                @$value(base64_decode($_GET[ $key ]), $app->container);
            } else {
                @$value();
            }
        }
    }
}
?>