<?php
/*                     *\
|	MYCMS - TProgram    |
\*                     */
$this->container['users']->hideIfNotLogged();

if(!$this->container['users']->currentUserHasPermission("edit_themes"))
{
    throw new MyCMS\App\Utils\Exceptions\MyCMSException("You do not have permission to access this page!", "Permission denied");
}

define('PAGE_ID', 'theme_manager');
define('PAGE_NAME', $this->container['languages']->ta('page_theme_manager', true));

$this->getFileAdmin('header');
$this->getPageAdmin('topbar');

$info = "";

if (isset($_GET['remove'])) {
    if (is_numeric($_GET['remove'])) {
        if ($this->container['users']->currentUserHasPermission("delete_themes")) {
            $info = $this->container['database']->row("SELECT * FROM my_style WHERE style_id = :style_id LIMIT 1", ['style_id' => $this->container['security']->mySqlSecure($_GET['remove'])]);
            $style_path_name = $info['style_path_name'];

            if ($info['style_enable_remove'] == '1') {

                $this->container['functions']->removeDir(FILE . '/src/App/Content/Theme/' . $style_path_name);
                $this->container['database']->query('DELETE FROM my_style WHERE style_id = :style_id LIMIT 1', ['style_id' => $this->container['security']->mySqlSecure($_GET['remove'])]);


                $site_language = $this->container['settings']->getSettingsValue('site_language');

                if ($this->container['settings']->saveSettings('site_template', 'my_cms_default') == false) {
                    define("INDEX_ERROR", $this->container['languages']->ea('error_page_settings_general_save', '1'));
                };
                if ($this->container['settings']->saveSettings('site_template_language', $site_language) == false) {
                    define("INDEX_ERROR", $this->container['languages']->ea('error_page_settings_general_save', '1'));
                };
            }

            header("location: " . HOST . "/my-admin/theme_manager");

        }
    }
}

$info_page = false;

if (isset($_GET['info'])) {
    if (is_numeric($_GET['info'])) {
        $info_page = true;
        $info_id = $this->container['security']->mySqlSecure($_GET['info']);
    }
}

if (isset($_POST['set_theme'])) {

    if ($this->container['users']->userLoggedIn()) {
        if ($this->container['users']->currentUserHasPermission("switch_themes")) {
            if (isset($_POST['style_path_name'])) {
                $style_path_name = $this->container['security']->mySqlSecure($_POST['style_path_name']);
                if ($this->container['settings']->getSettingsValue("site_template") != $style_path_name) {
                    if ($this->container['settings']->saveSettings('site_template', $style_path_name) == false) {
                        define("INDEX_ERROR", $this->container['languages']->ea('error_page_settings_general_save', '1'));
                    };
                }
            }
        }
    }

}

if (isset($_POST['newtheme'])) {
    if ($this->container['users']->userLoggedIn()) {
        if ($this->container['users']->currentUserHasPermission("install_themes"))
        {

            $jsonurl = htmlentities($_POST['jsonurl']);
            $info = $this->container['theme']->downloadTheme($jsonurl);
            if ($info == true) {
                $info = null;
            }
        }
    }
}

if (isset($_POST['uploadTheme'])) {
    if ($this->container['users']->userLoggedIn()) {
        if ($this->container['users']->currentUserHasPermission("upload_themes"))
        {
            $info = $this->container['theme']->installTheme($_FILES['themeFile']);
            if ($info == true) {
                $info = null;
            } else {
                $this->container['functions']->removeDir("." . MY_BASE_PATH . "/tmp/");
            }
        } else {
            define("INDEX_ERROR", $this->container['languages']->ea('error_admin_permissions', '1'));
        }
    }
}


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
            <?php if (!empty($info)) {
                echo '<br>' . $info . '<br>';
            } ?>
            <h1 class="h1PagesTitle"><?php $this->container['languages']->ea('page_theme_manager_header'); ?></h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <div class="row">
        <?php if ($info_page) { ?>
            <?php
            $new_update_new_cms = false;

            $info = $this->container['database']->row("SELECT * FROM my_style WHERE style_id = :style_id LIMIT 1", ['style_id' => $info_id]);

            $info_update = $this->container['theme']->themeUpdate($info['style_version'], $info['style_json_file_url']);
            if ($info_update[0] == true) {
                if ($info_update[2] == true) {
                    $new_update = false;//true
                    $new_update_new_cms = false;
                } else {
                    $new_update = false;
                    $new_update_new_cms = false;//true
                }
            } else {
                $new_update = false;
            }
            ?>
            <div class="col-lg-8" id="file_editor">
                <div class="col-lg-4" id="thumb">
                    <div class="well">
                        <div class="thumbnail">
                            <?php
                            if (isset($info) && file_exists(C_PATH . "/Theme/" . $info['style_path_name'] . "/screen.png")) {
                                ?>
                                <img src="{@siteURL@}/src/App/Content/Theme/<?php echo $info['style_path_name'] ?>/screen.png?<?php echo time(); ?>"
                                     class="screen" alt="<?php echo $template['style_name']; ?>">
                                <?php
                            } else {
                                ?>
                                <div style="width:100%; height:180px; background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAYAAADED76LAAAALElEQVQYGWO8d+/efwYkoKioiMRjYGBC4WHhUK6A8T8QIJt8//59ZC493AAAQssKpBK4F5AAAAAASUVORK5CYII=);">
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <div style="text-align: center;"><h3><?php echo $info['style_name']; ?></h3></div>
                    </div>
                </div>
                <div class="col-lg-8" id="div_file">
                    <div class="well">
                        <ul class="list-group" id="file_manager">
                            <?php
                            $file_dir = FILE . '/src/App/Content/Theme/' . $info['style_path_name'];
                            $file_and_dir_array = array_diff(scandir($file_dir, 0), ['..', '.']);
                            $dir_array = [];
                            $file_array = [];
                            foreach ($file_and_dir_array as $all_row) {
                                if (is_dir($file_dir . '/' . $all_row)) {
                                    $dir_array[] = $all_row;
                                } else {
                                    $file_array[] = $all_row;
                                }
                            }
                            foreach ($dir_array as $dir_row) {
                                ?>
                                <li class="list-group-item"><i class="fa fa-folder"></i> <a
                                            href="#<?php echo $dir_row; ?>"
                                            style="color: #563d7c;" <?php /* onclick="show_folder('<?php echo s_crypt($dir_row);?>')" */ ?>><b><?php echo $dir_row; ?></b></a>
                                </li>
                            <?php }
                            foreach ($file_array as $file_row) { ?>
                                <li class="list-group-item"><i class="fa fa-file"></i> <a
                                            href="#<?php echo $file_row; ?>" <?php /* onclick="show_file('<?php echo s_crypt($file_row);?>')" */ ?>><?php echo $file_row; ?></a>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-4" id="file_button">
                <div class="well well-sm">
                    <ul class="list-group" id="info-theme">
                        <li class="list-group-item">
                            <span class="badge"><?php echo $info['style_version']; ?></span>
                            <?php $this->container['languages']->ea('page_theme_manager_version_label'); ?>
                        </li>
                        <li class="list-group-item">
                            <span class="badge"><?php echo $info['style_author']; ?></span>
                            <?php $this->container['languages']->ea('page_theme_manager_author_label'); ?>
                        </li>
                    </ul>
                </div>
            <?php
            if ($this->container['users']->currentUserHasPermission("switch_themes")) {
            ?>
                    <div class="well well-sm" style="height: 52px;" id="info-theme-button">
                        <form action="" method="post">
                            <input type="hidden" name="style_path_name"
                                   value="<?php echo $info['style_path_name']; ?>"/>
                            <?php if ($this->container['settings']->getSettingsValue("site_template") != $info['style_path_name']) { ?>
                                <button style="float: right" type="submit" name="set_theme"
                                        class="btn btn-block btn-info"><?php $this->container['languages']->ea('page_theme_manager_set_button'); ?></button>
                            <?php } else { ?>
                                <button style="float: right" type="submit" name="" class="btn btn-block btn-info"
                                        disabled=""><?php $this->container['languages']->ea('page_theme_manager_set_button'); ?></button>
                            <?php } ?>
                        </form>
                    </div>
            <?php } ?>

                <?php
                if ($this->container['users']->currentUserHasPermission("edit_themes")) {
                    ?>
                    <?php if ($info['style_enable_remove'] == '1') { ?>
                        <div class="well well-sm" style="height: 52px;" id="info-theme-button">
                            <a href="{@siteURL@}/my-admin/theme_manager/remove/<?php echo $info['style_id'] ?>"
                               class="btn btn-sm btn-danger btn-block"
                               role="button"><?php $this->container['languages']->ea('page_theme_manager_button_remove'); ?></a>
                        </div>
                    <?php } ?>


                    <div class="panel b_panel">
                        <div class="panel-body b_panel-body">
                            <div class="panel-body-padding">

                                <a class="btn btn-block btn-danger btn-block"
                                   href="{@siteURL@}/my-admin/theme_customize?theme=<?php echo $info['style_path_name']; ?>"><?php $this->container['languages']->ea('page_theme_manager_customizer'); ?></a>
                                <a class="btn btn-block btn-primary btn-block"
                                   href="{@siteURL@}/my-admin/code_editor?theme=<?php echo $info['style_path_name']; ?>"><?php $this->container['languages']->ea('page_theme_manager_edit_in_code_editor'); ?></a>

                            </div>
                        </div>

                    </div>
                    <?php
                }
                ?>
                <?php if ($new_update == true) { ?>
                    <div class="well well-sm" style="height: 52px;">
                        Ciao
                    </div>
                <?php } ?>

                <?php if ($new_update_new_cms == true) { ?>
                    <div class="well well-sm" style="height: 52px;">
                        Ciao nuova
                    </div>
                <?php } ?>
            </div>
        <?php } else { ?>

            <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                <?php
                $row = false;
                $temp = $this->container['database']->query("SELECT * FROM my_style ORDER BY style_id DESC");
                $i = 0;
                foreach ($temp as $template) {

                    if ($i % 2 == 0 && $row == false) {
                        $row = true;
                        echo "<div class='row'>";
                    }
                    ?>
                    <div class="col-sm-6 col-md-6 col-xs-12">
                        <div class="thumbnail">
                            <?php
                            if (file_exists(C_PATH . "/Theme/" . $template['style_path_name'] . "/screen.png")) {
                                ?>
                                <img src="{@siteURL@}/src/App/Content/Theme/<?php echo $template['style_path_name'] ?>/screen.png?<?php echo time(); ?>"
                                     class="screen" alt="<?php echo $template['style_name']; ?>">
                                <?php
                            } else {
                                ?>
                                <div style="width:100%; height:180px; background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAYAAADED76LAAAALElEQVQYGWO8d+/efwYkoKioiMRjYGBC4WHhUK6A8T8QIJt8//59ZC493AAAQssKpBK4F5AAAAAASUVORK5CYII=);">
                                </div>
                                <?php
                            }
                            ?>
                            <div class="caption row" style="padding-bottom: 0">
                                <div class="col-md-6 col-sm-12 col-xs-6" style="overflow-x:hidden">
                                    <h4><?php echo $template['style_name']; ?></h4>
                                </div>
                                <div class="col-md-6 col-sm-12 col-xs-6">
                                    <div>
                                        <a href="{@siteURL@}/my-admin/theme_manager/info/<?php echo $template['style_id'] ?>"
                                           class="btn btn-sm btn-primary btn-block"
                                           role="button"><?php $this->container['languages']->ea('page_theme_manager_button_info'); ?></a>
                                        <!--<?php if ($template['style_enable_remove'] == '1') { ?> <a href="{@siteURL@}/my-admin/theme_manager/remove/<?php echo $template['style_id'] ?>" class="btn btn-sm btn-danger btn-block" role="button"><?php $this->container['languages']->ea('page_theme_manager_button_remove'); ?></a> <?php } ?>-->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    if ($i % 2 != 0 && $row == true) {
                        $row = false;
                        echo "</div>";
                    }
                    $i++;
                }
                if ($row == true) {
                    $row = false;
                    echo "</div>";
                }
                ?>
            </div>
            <div class="col-lg-4 col-sm-5 col-md-4 col-xs-12">
                <?php
                if ($this->container['users']->currentUserHasPermission("upload_themes")) {
                    ?>
                <div class="panel b_panel">
                    <div class="panel-heading">
                        <h1 class="panel-title text-center"><?php $this->container['languages']->ea('page_theme_manager_upload_new_theme'); ?></h1>
                    </div>

                        <form enctype="multipart/form-data" method="post">
                            <div class="panel-body text-center">
                                <div class="form-group">
                                    <label style="font-weight: normal;"><?php $this->container['languages']->ea('page_theme_manager_placeholder_upload'); ?></label>
                                    <input type="file" id="themeFile" name="themeFile">
                                </div>
                            </div>
                            <button type="submit" name="uploadTheme"
                                    class="btn btn-block btn-primary b_btn b_btn_radius"><?php $this->container['languages']->ea('page_theme_manager_upload_button'); ?></button>
                        </form>
                </div>
                <?php
            }
            ?>

                <?php
                if ($this->container['users']->currentUserHasPermission("install_themes")) {
                ?>
                <div class="panel b_panel">
                    <div class="panel-heading">
                        <h1 class="panel-title text-center"><?php $this->container['languages']->ea('page_theme_manager_add_new_theme'); ?></h1>
                    </div>
                    <form action="" method="post">
                        <div class="panel-body">
                            <input type="text" name="jsonurl" class="form-control b_form-control"
                                   placeholder="<?php $this->container['languages']->ea('page_theme_manager_labe_json_url'); ?>" maxlength="200">
                            <br/>

                        </div>
                        <button type="submit" name="newtheme"
                                class="btn btn-block btn-primary b_btn b_btn_radius"><?php $this->container['languages']->ea('page_theme_manager_add_button'); ?></button>
                    </form>
                </div>
                    <?php
                }
                ?>
            </div>
        <?php } ?>
    </div>
    <!-- /.row -->
</div>
<!-- /#page-wrapper -->

</div>
<!-- /#wrapper -->
<?php $this->getFileAdmin('footer'); ?>

</body>

</html>
