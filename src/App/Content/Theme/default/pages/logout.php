<?php
    if (!isset($_GET['return_url'])) {
        $return_url = "";
    } else {
        $return_url = my_sql_secure(base64_decode($_GET['return_url']));
        $return_url_und = my_sql_secure($_GET['return_url']);
    }
    $this->container['users']->logout($return_url);
?>
