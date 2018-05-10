<?php
/*                     *\
|	MYCMS - TProgram    |
\*                     */

$this->container['users']->hideIfNotLogged();

if(!$this->container['users']->currentUserHasPermission("manage_options"))
{
    throw new MyCMS\App\Utils\Exceptions\MyCMSException("You do not have permission to access this page!", "Permission denied");
}

define('PAGE_ID', 'admin_xml_command');
define('PAGE_NAME', $this->container['languages']->ea('page_settings_page_name', '1') . ': ' . $this->container['languages']->ea('page_settings_xml_command', '1'));

$this->getFileAdmin('header');
$this->getPageAdmin('topbar');


?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="h1PagesTitle"><?php $this->container['languages']->ea('page_settings_xml_command_header'); ?></h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-6">
            <form role="form" method="post" action="">
                <div class="form-group">
                    <label><?php $this->container['languages']->ea('page_settings_xml_command_text'); ?></label>
                    <div class="form-group">
                        <textarea name="xml_command_code" style="height:210px;width:100%;"></textarea>
                    </div>
                </div>

                <input type="submit" name="save_settings_xml_commands" class="btn btn-success"
                       value="<?php $this->container['languages']->ea('page_settings_xml_button'); ?>"/>


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

