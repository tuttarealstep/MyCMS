<?php

$this->container['users']->hideIfStaffNotLogged();

define('PAGE_ID', 'admin_upload');
define('PAGE_NAME', $this->container['languages']->ta('page_upload', true));

$this->getFileAdmin('header');
$this->getPageAdmin('topbar');

?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="h1PagesTitle"><?php $this->container['languages']->ea('page_upload_header'); ?>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
        </div>
    </div>
</div>

<?php $this->getFileAdmin('footer'); ?>

