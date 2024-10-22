<?php
$this->container['users']->hideIfNotLogged();

if(!$this->container['users']->currentUserHasPermission("import"))
{
    throw new MyCMS\App\Utils\Exceptions\MyCMSException("You do not have permission to access this page!", "Permission denied");
}

define('PAGE_ID', 'admin_pages_edit');
define('PAGE_NAME', $this->container['languages']->ea('page_pages_import_title', '1'));

$this->getFileAdmin('header');
$this->getPageAdmin('topbar');


$this->getStyleScriptAdmin('script');
?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="h1PagesTitle"><?php $this->container['languages']->ea('page_pages_import_title_head'); ?></h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-6">
            <form role="form" method="post" action="">
                <div class="form-group">
                    <div class="form-group">
                        <textarea name="json_code" style="height:210px; width:100%;"></textarea>
                    </div>
                </div>
                <input type="submit" name="import_page_json" class="btn btn-success btn-block"
                       value="<?php $this->container['languages']->ea('page_pages_import_button'); ?>"/>
            </form>
        </div>
    </div>
    <!-- /.row -->
</div>
<!-- /#page-wrapper -->

</div>
<!-- /#wrapper -->

<?php $this->getFileAdmin('footer'); ?>

</body>

</html>