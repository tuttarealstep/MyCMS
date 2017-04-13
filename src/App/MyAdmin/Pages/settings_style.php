<?php
    /*                     *\
    |	MYCMS - TProgram    |
    \*                     */

    hide_if_staff_not_logged();

    define('PAGE_ID', 'admin_settings_style');
    define('PAGE_NAME', ea('page_settings_page_name', '1') . ': ' . ea('page_settings_style', '1'));

    get_file_admin('header');
    get_page_admin('topbar');

    $language_language = get_settings_value('site_language');
    $settings_style_template_cms = get_settings_value('site_template');
    $settings_style_template_language = get_settings_value('site_template_language');

    global $my_db;

?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="h1PagesTitle"><?php ea('page_settings_style_header'); ?></h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-6">
            <form role="form" method="post" action="">
                <div class="form-group">
                    <label><?php ea('page_settings_my_admin_language'); ?></label>
                    <select name="settings_style_language" id="settings_style_language" class="form-control">
                        <?php
                            $lang = $my_db->query("SELECT * FROM my_language");
                            $i = 0;
                            foreach ($lang as $language) {
                                $i++;
                                ?>
                                <option <?php if ($language_language == $language['language_language']) {
                                    echo 'selected=""';
                                } ?> value="<?php echo $language['language_language']; ?>"><?php echo $language['language_name']; ?></option>
                                <?php
                            }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label><?php ea('page_settings_style_template'); ?></label>
                    <select name="settings_style_template" id="settings_style_template" class="form-control">
                        <?php
                            $temp = $my_db->query("SELECT * FROM my_style");
                            $i = 0;
                            foreach ($temp as $template) {
                                $i++;
                                ?>
                                <option <?php if ($settings_style_template_cms == $template['style_path_name']) {
                                    echo 'selected=""';
                                } ?> value="<?php echo $template['style_path_name']; ?>"><?php echo $template['style_name']; ?></option>
                                <?php
                            }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label><?php ea('page_settings_theme_language'); ?>
                        <small>(<?php ea('page_settings_theme_language_info'); ?>)</small>
                    </label>
                    <select name="settings_style_template_language" id="settings_style_template_language"
                            class="form-control">
                        <?php
                            $temp = $my_db->row("SELECT * FROM my_style WHERE style_path_name = :style_path_name", ['style_path_name' => $settings_style_template_cms]);
                            $language = explode(',', $temp['style_languages']);
                            for ($i = 0; $i < count($language); $i++) {
                                ?>
                                <option <?php if ($settings_style_template_language == $language[ $i ]) {
                                    echo 'selected=""';
                                } ?> value="<?php echo $language[ $i ]; ?>"><?php echo $language[ $i ]; ?></option>
                                <?php
                            }
                        ?>
                    </select>
                </div>
                <input type="submit" name="save_settings_style" class="btn btn-success"
                       value="<?php ea('page_settings_site_button_save'); ?>"/>
            </form>
        </div>
    </div>
    <!-- /.row -->
</div>
<!-- /#page-wrapper -->

</div>
<!-- /#wrapper -->

<?php get_file_admin('footer'); ?>

</body>

</html>

