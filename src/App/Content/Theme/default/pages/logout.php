<?php
if (!isset($_GET['return_url'])) {
    $return_url = "";
} else {
    $return_url = mySqlSecure(base64_decode($_GET['return_url']));
    $return_url_und = mySqlSecure($_GET['return_url']);
}
$this->container['users']->logout($return_url);
?>
