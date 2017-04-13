<?php

    hide_if_staff_not_logged();

    $_SESSION["customizer"] = true;
    $_SESSION['customizerLastAction'] = time();

    global $my_date, $my_db, $my_users, $my_blog, $my_theme;

    $user_rank = $my_users->getInfo($_SESSION['staff']['id'], 'rank');
    if ($user_rank < 3)
    {
        header('Location: ' . HOST . '/my-admin/home');
        exit();
    }

    define('PAGE_ID', 'admin_theme_customize');
    define('PAGE_NAME', ea('page_admin_theme_customize_page_name', '1'));
    define('NO_VIEWPORT', true);

    get_file_admin('header');
    get_page_admin('topbar');

    if (isset($_GET['theme']) && !empty($_GET['theme'])) {
        if ($this->container['theme']->theme_exist($_GET['theme'])) {
            $theme = $_GET['theme'];
        } else {
            $theme = "default";
        }
    } else {
        $theme = "default";
    }
?>
<script>
    var theme_var = "<?php echo $theme; ?>";
    var t_exit_now = "<?php ea('page_admin_theme_customize_t_exit_now'); ?>";
</script>
<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,700italic'
      rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/customizer/customizer.css">
<form>
    <div class="customizer" id="customizer">
        <div class="left_panel">
            <div class="container" style="height: 100%;">
                <div class="left_panel_menu">
                    <div class="left_panel_menu_title row">
                        <div class="pull-left">
                            <a class="btn" onclick="closeCustomizer();" href="#"><i
                                        class="fa fa-times-circle fa-fw"></i></a>
                        </div>
                        <div class="pull-right">
                            <a class="btn"
                               onclick="saveCustomizer();"><?php ea('page_admin_theme_customize_save'); ?></a>
                        </div>
                    </div>
                    <div class="left_panel_menu_content">
                        <div class="info_div" style="line-height: 1.8;">
                                    <span class="small_text">
                                        <?php ea('page_admin_theme_customize_you_are_customizing'); ?><br>
                                        <strong><?php echo $theme; ?></strong>
                                    </span>
                        </div>
                        <div class="info_div spaceTop noLRMargin">
                            <?php $this->container['plugins']->applyEvent('customizerCustomMenu'); ?>
                        </div>
                    </div>
                    <div class="left_panel_menu_bottom row">
                        <div class="pull-right">
                            <a id="displayMobile" class="btn" style="border-left: 1px solid #424242;"><i
                                        class="fa fa-mobile fa-fw"></i></a>
                            <a id="displayTablet" class="btn"><i class="fa fa-tablet fa-fw"></i></a>
                            <a id="displayDesktop" class="btn"><i class="fa fa-desktop fa-fw"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php get_file_admin('footer'); ?>
        <script id="js_to_ex" src="{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/my_text_editor/base64.js"></script>
        <?php $this->container['plugins']->applyEvent('customizerJsMenu'); ?>
        <script id="js_to_ex" src="{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/customizer/customizer.js"></script>
        <?php $this->container['plugins']->applyEvent('customizerOthersJs'); ?>
        <div class="right_panel" id="previewFrameContainer">
            <iframe id="previewFrame" onLoad="checkIfPageIsCustomizable();">

            </iframe>
        </div>
    </div>
</form>
</body>

</html>
