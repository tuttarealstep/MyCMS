<?php
/*                     *\
|	MYCMS - TProgram    |
\*                     */

$this->container['users']->hideIfStaffNotLogged();

define('PAGE_ID', 'admin_settings_style');
define('PAGE_NAME', $this->container['languages']->ea('page_settings_page_name', '1') . ': ' . $this->container['languages']->ea('page_settings_style', '1'));

$this->getFileAdmin('header');
$this->getPageAdmin('topbar');

$language_language = $this->container['settings']->getSettingsValue('site_language');
$settings_style_template_cms = $this->container['settings']->getSettingsValue('site_template');
$settings_style_template_language = $this->container['settings']->getSettingsValue('site_template_language');



?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="h1PagesTitle"><?php $this->container['languages']->ea('page_settings_style_header'); ?></h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-6">
            <form role="form" method="post" action="">
                <div class="form-group">
                    <label><?php $this->container['languages']->ea('page_settings_my_admin_language'); ?></label>
                    <select name="settings_style_language" id="settings_style_language" class="form-control">
                        <?php
                        $lang = $this->container['database']->query("SELECT * FROM my_language");
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
                    <label><?php $this->container['languages']->ea('page_settings_style_template'); ?></label>
                    <select name="settings_style_template" id="settings_style_template" class="form-control">
                        <?php
                        $temp = $this->container['database']->query("SELECT * FROM my_style");
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
                    <label><?php $this->container['languages']->ea('page_settings_theme_language'); ?>
                        <small>(<?php $this->container['languages']->ea('page_settings_theme_language_info'); ?>)</small>
                    </label>
                    <select name="settings_style_template_language" id="settings_style_template_language"
                            class="form-control">
                        <?php
                        $temp = $this->container['database']->row("SELECT * FROM my_style WHERE style_path_name = :style_path_name", ['style_path_name' => $settings_style_template_cms]);
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
                       value="<?php $this->container['languages']->ea('page_settings_site_button_save'); ?>"/>
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

