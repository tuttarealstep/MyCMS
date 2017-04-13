<?php
    define('MY_CMS_PATH', true);
    define("LOADER_LOAD_PAGE", false);
    include '../../../../src/Bootstrap.php';

    if (staff_logged_in()) {
        foreach ($App->container["themeCustomizer"]->saveCallBackVector as $key => $value) {
            //echo $key . " " . base64_decode($_GET[$key]) . "<br>";
            if (isset($_GET[ $key ])) {
                @$value(base64_decode($_GET[ $key ]), $App->container);
            } else {
                @$value();
            }
        }
    }
?>