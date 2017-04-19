<?php
/*                     *\
|	MYCMS - TProgram    |
\*                     */

hideIfStaffNotLogged();

define('PAGE_ID', 'admin_settings_blog');
define('PAGE_NAME', ea('page_settings_page_name', '1') . ': ' . ea('page_settings_blog', '1'));

getFileAdmin('header');
getPageAdmin('topbar');
?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="h1PagesTitle"><?php ea('page_settings_blog_header'); ?></h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-6">
            <form role="form" method="post" action="">
                <div class="form-group">
                    <label><?php ea('page_settings_blog_private'); ?></label>
                    <select name="settings_blog_private" id="settings_blog_private" class="form-control">
                        <option <?php if (getSettingsValue('blog_private') == 'true') {
                            echo 'selected=""';
                        } ?> value="true"><?php ea('page_settings_blog_private_off'); ?></option>
                        <option <?php if (getSettingsValue('blog_private') == 'false') {
                            echo 'selected=""';
                        } ?> value="false"><?php ea('page_settings_blog_private_on'); ?></option>
                    </select>
                </div>

                <div class="form-group">
                    <label><?php ea('page_settings_blog_comments_active'); ?></label>
                    <select name="settings_blog_comments_active" id="settings_blog_comments_active"
                            class="form-control">
                        <option <?php if (getSettingsValue('blog_comments_active') == 'true') {
                            echo 'selected=""';
                        } ?> value="true"><?php ea('page_settings_blog_comments_active_on'); ?></option>
                        <option <?php if (getSettingsValue('blog_comments_active') == 'false') {
                            echo 'selected=""';
                        } ?> value="false"><?php ea('page_settings_blog_comments_active_off'); ?></option>
                    </select>
                </div>

                <div class="form-group">
                    <label><?php ea('page_settings_blog_comments_approve'); ?></label>
                    <select name="settings_blog_comments_approve" id="settings_blog_comments_active"
                            class="form-control">
                        <option <?php if (getSettingsValue('blog_comments_approve') == 'true') {
                            echo 'selected=""';
                        } ?> value="true"><?php ea('page_settings_blog_comments_approve_on'); ?></option>
                        <option <?php if (getSettingsValue('blog_comments_approve') == 'false') {
                            echo 'selected=""';
                        } ?> value="false"><?php ea('page_settings_blog_comments_approve_off'); ?></option>
                    </select>
                </div>


                <input type="submit" name="save_settings_blog" class="btn btn-success"
                       value="<?php ea('page_settings_site_button_save'); ?>"/>


            </form>
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

