<?php
$this->container['users']->hideIfNotLogged();

define('PAGE_ID', 'admin_posts');
define('PAGE_NAME', $this->container['languages']->ta('page_plugins_title', true));

$this->getFileAdmin('header');
$this->getPageAdmin('topbar');
?>
<?php $this->getFileAdmin('footer'); ?>

