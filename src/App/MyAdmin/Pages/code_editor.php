<?php
/**
 * User: tuttarealstep
 * Date: 30/06/16
 * Time: 22.31
 */

$this->container['users']->hideIfStaffNotLogged();

$user_rank = $this->container["users"]->getInfo($_SESSION['staff']['id'], 'rank');
if ($user_rank < 3) {
    header('Location: ' . HOST . '/my-admin/home');
    exit();
}

define('PAGE_ID', 'admin_code_editor');
define('PAGE_NAME', $this->container['languages']->ea('page_admin_code_editor_page_name', '1'));

$this->getFileAdmin('header');
$this->getPageAdmin('topbar');

if (isset($_GET['theme'])) {
    if ($this->container['theme']->themeExist($_GET['theme'])) {
        $theme = $_GET['theme'];
    } else {
        $theme = "";
    }
}
?>
<script>
    var theme_var = "<?php echo $theme; ?>";
    var _t_theme_not_found = "<?php $this->container['languages']->ea('page_admin_code_editor_theme_not_found'); ?>"
    var _t_create_new_file = "<?php $this->container['languages']->ea('page_admin_code_editor_create_new_file'); ?>";
    var _t_file_name = "<?php $this->container['languages']->ea('page_admin_code_editor_t_file_name'); ?>";
    var _t_file_created = "<?php $this->container['languages']->ea('page_admin_code_editor_t_file_created'); ?>";
    var _t_file_not_created = "<?php $this->container['languages']->ea('page_admin_code_editor_t_file_not_created'); ?>";
    var _t_save_file = "<?php $this->container['languages']->ea('page_admin_code_editor_t_save_file'); ?>";
    var _t_file_saved = "<?php $this->container['languages']->ea('page_admin_code_editor_t_file_saved'); ?>";
    var _t_line = "<?php $this->container['languages']->ea('page_admin_code_editor_t_line'); ?>";
    var _t_column = "<?php $this->container['languages']->ea('page_admin_code_editor_t_column'); ?>";
    var _t_if_you_confirm = "<?php $this->container['languages']->ea('page_admin_code_editor_t_if_you_confirm'); ?>";
    var _t_file_loaded = "<?php $this->container['languages']->ea('page_admin_code_editor_t_file_loaded'); ?>";
    var _t_setup_complete = "<?php $this->container['languages']->ea('page_admin_code_editor_t_setup_complete'); ?>";
    var _t_are_you_sure = "<?php $this->container['languages']->ea('page_admin_code_editor_t_are_you_sure'); ?>";
</script>
<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,700italic'
      rel='stylesheet' type='text/css'>

<script src="{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/codemirror-5.16.0/lib/codemirror.js"></script>
<link rel="stylesheet" href="{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/codemirror-5.16.0/lib/codemirror.css">
<link rel="stylesheet" href="{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/codemirror-5.16.0/theme/dracula.css">
<script src="{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/codemirror-5.16.0/mode/javascript/javascript.js"></script>
<script src="{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/codemirror-5.16.0/mode/php/php.js"></script>
<script src="{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/codemirror-5.16.0/mode/xml/xml.js"></script>
<script src="{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/codemirror-5.16.0/mode/css/css.js"></script>
<script src="{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/codemirror-5.16.0/mode/htmlembedded/htmlembedded.js"></script>
<script src="{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/codemirror-5.16.0/mode/htmlmixed/htmlmixed.js"></script>
<script src="{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/codemirror-5.16.0/mode/sass/sass.js"></script>
<script src="{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/codemirror-5.16.0/mode/twig/twig.js"></script>
<script src="{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/codemirror-5.16.0/addon/display/fullscreen.js"></script>
<script src="{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/codemirror-5.16.0/addon/edit/closebrackets.js"></script>
<script src="{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/codemirror-5.16.0/addon/edit/closetag.js"></script>
<script src="{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/codemirror-5.16.0/addon/hint/css-hint.js"></script>
<script src="{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/codemirror-5.16.0/addon/hint/html-hint.js"></script>
<script src="{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/codemirror-5.16.0/addon/hint/xml-hint.js"></script>
<script src="{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/codemirror-5.16.0/addon/hint/show-hint.js"></script>
<link rel="stylesheet" href="{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/codemirror-5.16.0/addon/hint/show-hint.css">
<script src="{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/codemirror-5.16.0/addon/selection/active-line.js"></script>

<script src="{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/codemirror-5.16.0/addon/search/searchcursor.js"></script>
<script src="{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/codemirror-5.16.0/addon/search/search.js"></script>
<script src="{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/codemirror-5.16.0/addon/search/jump-to-line.js"></script>

<script src="{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/codemirror-5.16.0/addon/dialog/dialog.js"></script>
<link rel="stylesheet" href="{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/codemirror-5.16.0/addon/dialog/dialog.css">

<script src="{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/codemirror-5.16.0/addon/scroll/simplescrollbars.js"></script>
<link rel="stylesheet"
      href="{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/codemirror-5.16.0/addon/scroll/simplescrollbars.css">

<link rel="stylesheet" href="{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/my_text_editor/perfect-scrollbar.min.css">
<link rel="stylesheet" href="{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/my_text_editor/my_text_editor.css">
<div class="container">
    <br>
    <div class="row">
        <div class="col-lg-10">
            <!-- Text editor -->
            <form>
                <div class="my_text_editor">
                    <div class="topbar_panel" style="z-index: 1002;">
                        <div class="container-fluid">
                            <div class="pull-left">
                                <p><b>MyTextEditor: </b> <i id="projectTitle"></i></p>
                            </div>
                            <div class="pull-right">
                                <ul style="list-style: none;">
                                    <li class="dropdown">
                                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                            <i class="fa fa-gear fa-fw"></i> <i class="fa fa-caret-down"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-user" style="left: -126px; ">
                                            <li>
                                                <a href="{@siteURL@}/my-admin/theme_manager"
                                                   onclick="return confirm(_t_are_you_sure)"><i
                                                            class="fa fa-sign-out fa-fw"></i> <?php $this->container['languages']->ea('page_admin_code_editor_return_back'); ?>
                                                </a>
                                            </li>
                                            <li class="divider"></li>
                                            <li>
                                                <a onclick="saveCurrentFile()"><i
                                                            class="fa fa-save fa-fw"></i> <?php $this->container['languages']->ea('page_admin_code_editor_save_current_file'); ?>
                                                </a>
                                            </li>
                                            <li>
                                                <a onclick="newFile()"><i
                                                            class="fa fa-file fa-fw"></i> <?php $this->container['languages']->ea('page_admin_code_editor_new_file'); ?>
                                                </a>
                                            </li>
                                        </ul>
                                        <!-- /.dropdown-user -->
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="left_panel">
                        <div class="container" style="height: 100%;">
                            <div id="file_manager"></div>
                        </div>
                    </div>
                    <textarea id="div_code_mirror" style="display: none;" rows="210"></textarea>

                    <div class="bottombar_panel">
                        <div class="container-fluid">
                            <div class="pull-left">
                                <p id="lineInfo"></p>
                            </div>
                            <div class="pull-left" style="line-height: 1.8">
                                <p>&nbsp; | &nbsp;</p>
                            </div>
                            <div class="pull-left">
                                <p id="editorInfo"></p>
                            </div>
                            <div class="pull-right">
                                <p id="editorMode"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Text editor -->
        </div>
    </div>
    <!-- /.row -->
</div>
<!-- /#page-wrapper -->
</div>
<!-- /#wrapper -->
<?php $this->getFileAdmin('footer'); ?>

<script id="js_to_ex" src="{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/my_text_editor/base64.js"></script>
<script src="{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/my_text_editor/perfect-scrollbar.jquery.min.js"></script>
<script id="js_to_ex" src="{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/my_text_editor/my_text_editor.js"></script>

</body>

</html>
