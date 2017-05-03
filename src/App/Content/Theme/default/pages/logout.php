<?php
if (!isset($_GET['return_url'])) {
    $return_url = "";
} else {
    $return_url = $this->container['security']->mySqlSecure(base64_decode($_GET['return_url']));
    $return_url_und = $this->container['security']->mySqlSecure($_GET['return_url']);
}
$this->container['users']->logout($return_url);
?>
