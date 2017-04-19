<?php
hideIfStaffNotLogged();

define('PAGE_ID', 'admin_settings_user');
define('PAGE_NAME', ea('page_settings_page_name', '1') . ': ' . ea('page_settings_user', '1'));

getFileAdmin('header');
getPageAdmin('topbar');


global $my_db;
?>
<?php
if (defined("INDEX_ERROR")) {
    ?>
    <div class="container">
        <div class="panel" style="padding: 8px; border-bottom: 3px solid #b71c1c; margin-top: 2%">
            <div class="panel-body login-panel-body">
                <?php echo INDEX_ERROR; ?>
            </div>
        </div>
    </div>
    <?php
}
?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="h1PagesTitle"><?php ea('page_settings_user_header'); ?></h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6">
            <form role="form" method="post" action="">
                <div class="form-group">
                    <label><?php ea('page_settings_user_change_old_password'); ?></label>
                    <input class="form-control b_form-control"
                           placeholder="<?php ea('page_settings_user_placeholder_old_password') ?>" name="password"
                           type="password" value="" required>
                </div>
                <div class="form-group">
                    <label><?php ea('page_settings_user_change_new_password'); ?></label>
                    <input class="form-control b_form-control"
                           placeholder="<?php ea('page_settings_user_placeholder_new_password') ?>" name="new_password"
                           type="password" value="" required>
                </div>
                <div class="form-group">
                    <label><?php ea('page_settings_user_change_password_repeat'); ?></label>
                    <input class="form-control b_form-control"
                           placeholder="<?php ea('page_settings_user_placeholder_password_repeat') ?>"
                           name="password_repeat" type="password" value="" required>
                </div>
                <input type="submit" class="btn btn-primary btn-block b_btn" name="page_settings_user_save_button"
                       value="<?php ea('page_settings_user_save_button') ?>"/>
            </form>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6">
            <b><?php ea('page_settings_user_change_admin_style'); ?></b>

            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <a id="changeLayout1" href="#" rel="{@MY_ADMIN_TEMPLATE_PATH@}/Assets/css/MyAdmin.css">
                        <div style="height: 40px; margin-top: 10px; border-radius: 4px; border: 2px solid #424242">
                            <table style="height: 100%;">
                                <tr>
                                    <td style="width: 100px; background-color: #424242"></td>
                                    <td style="width: 100px; background-color: #286090"></td>
                                    <td style="width: 100px; background-color: #337ab7"></td>
                                    <td style="width: 100px; background-color: #FAFAFA"></td>
                                    <td style="width: 100px; background-color: #FFFFFF"></td>
                                </tr>
                            </table>
                        </div>
                    </a>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <a id="changeLayout2" href="#" rel="{@MY_ADMIN_TEMPLATE_PATH@}/Assets/css/MyAdminLight.css">
                        <div style="height: 40px; margin-top: 10px; border-radius: 4px; border: 2px solid #424242">
                            <table style="height: 100%;">
                                <tr>
                                    <td style="width: 100px; background-color: #4C5454"></td>
                                    <td style="width: 100px; background-color: #FF5964"></td>
                                    <td style="width: 100px; background-color: #FFF"></td>
                                    <td style="width: 100px; background-color: #1EA896"></td>
                                    <td style="width: 100px; background-color: #DBDBDB"></td>
                                </tr>
                            </table>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- /.row -->
</div>
<!-- /#page-wrapper -->

</div>
<!-- /#wrapper -->

<?php getFileAdmin('footer'); ?>

</body>

</html>

