<?php
    /*                     *\
    |	MYCMS - TProgram    |
    \*                     */

    global $my_db, $my_users, $my_blog, $my_theme;
    hide_if_staff_not_logged();



$user_rank = $this->container["users"]->getInfo($_SESSION['staff']['id'], 'rank');
if ($user_rank < 3)
{
    header('Location: ' . HOST . '/my-admin/home');
    exit();
}

    define('PAGE_ID', 'admin_pages_edit');
    define('PAGE_NAME', ea('page_pages_edit', '1'));

    add_style_script_admin('script', '{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/tinymce/tinymce.min.js');

    get_file_admin('header');
    get_page_admin('topbar');
$this->container['plugins']->applyEvent('myPageEditAfterTopBar');
$this->container['plugins']->applyEvent('myPageNewEditAfterTopBar');


    if (isset($_GET['id'])) {

        if (is_numeric($_GET['id'])) {
            if ($my_db->single("SELECT count(*) FROM my_page WHERE pageID = '" . $_GET['id'] . "' LIMIT 1") > 0) {
                $pageid = my_sql_secure($_GET['id']);
                $pages['title'] = remove_space($my_db->single("SELECT pageTITLE FROM my_page WHERE pageID = '" . $_GET['id'] . "' LIMIT 1"));
                $pages['content'] = $my_db->single("SELECT pageHTML FROM my_page WHERE pageID = '" . $_GET['id'] . "' LIMIT 1");
                $pages['URL'] = $my_db->single("SELECT pageURL FROM my_page WHERE pageID = '" . $_GET['id'] . "' LIMIT 1");
                $pagePUBLIC = $my_db->single("SELECT pagePUBLIC FROM my_page WHERE pageID = '" . $_GET['id'] . "' LIMIT 1");
                $pagePUBLICLabel = ($pagePUBLIC == "1") ? ea('page_pages_status_publish', '1') : ea('page_pages_status_draft', '1');

            }
        } else {
            header('Location: ' . HOST . '/my-admin/home');
            exit();
        }

    } else {

        header('Location: ' . HOST . '/my-admin/home');
        exit();

    }

$this->container['plugins']->addEvent('parseMyPageContent', function ($content)
{
    return $content;
});


if (isset($_POST['pages_new_create'])) {
        if (!empty($_POST['pages_title'])) {
            $pages_title = add_space(addslashes($_POST['pages_title']));
            $pages_content = $this->container['plugins']->applyEvent('parseMyPageContent', $_POST['pages_content']);


            $pagePUBLIC = addslashes($_POST['pagePUBLIC']);
            $pagePUBLICLabel = ($pagePUBLIC == "1") ? ea('page_pages_status_publish', '1') : ea('page_pages_status_draft', '1');


            $pages_menu_id = my_generate_random(5) . $pages_title;

            $my_db->query("UPDATE my_page SET pageTITLE = '$pages_title', pagePUBLIC = '$pagePUBLIC', pageHTML = '$pages_content'WHERE pageID = '" . $pageid . "'");;
            $info = '<div class="row"><div class="alert alert-success">' . ea('page_pages_edit_success_created', '1') . ' <a href="' . $pages['URL'] . '">' . ea('page_pages_edit_success_show', '1') . '</a></div>';
            $pages['title'] = $_POST['pages_title'];
            $pages['content'] = $this->container['plugins']->applyEvent('parseMyPageContent', $_POST['pages_content']);
        } else {
            $pagePUBLIC = addslashes($_POST['pagePUBLIC']);
            $pagePUBLICLabel = ($pagePUBLIC == "1") ? ea('page_pages_status_publish', '1') : ea('page_pages_status_draft', '1');

            $pages['content'] = $this->container['plugins']->applyEvent('parseMyPageContent', $_POST['pages_content']);
            define("INDEX_ERROR", ea('page_pages_edit_error_title', '1'));

        }
    }
    get_style_script_admin('script');
?>
<script type="text/javascript">
    tinymce.init({
        selector: "textarea",
        language_url: '{@siteURL@}/src/App/MyAdmin/languages/{@siteLANGUAGE@}.js',
        plugins: [
            "advlist autolink lists link image charmap print preview anchor",
            "searchreplace visualblocks code fullscreen",
            "insertdatetime media table contextmenu paste textcolor"
        ],

        toolbar: "insertfile undo redo | styleselect forecolor backcolor |  bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
        autosave_ask_before_unload: false
    });
</script>
<style>
    .panel-heading .accordion-toggle:after {
        font-family: 'Glyphicons Halflings';
        content: "\e114";
        float: right;
        color: grey;
    }

    .panel-heading .accordion-toggle.collapsed:after {
        content: "\e080";
    }
</style>
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
            <h1 class="h1PagesTitle"><?php ea('page_pages_edit_header'); ?></h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <form role="form" method="post" action="">
        <div class="row">
            <div class="col-lg-9 col-md-9 col-sm-8 col-xs-12">
                <div class="panel b_panel">
                    <div class="panel-body b_panel-body panel-body-padding">
                <div class="form-group">
                    {@noTAGS_start@}
                    <input type="text" name="pages_title" id="title" class="form-control b_form-control" maxlength="100"
                           value="<?php echo $pages['title']; ?>" placeholder="<?php ea('page_pages_edit_title'); ?>">
                    {@noTAGS_end@}
                </div>
                <br/>
                <div class="addons-menu">
                    <?php $this->container['plugins']->applyEvent('myPageAddonsMenu'); ?>
                </div>
                <br>
                <div class="form-group">
                    {@noTAGS_start@}
                    <textarea name="pages_content"
                              style="height:300px;"><?php echo $my_theme->no_tags($pages['content']); ?></textarea>
                    {@noTAGS_end@}
                </div>
                    </div>
                </div>
            </div>
            <!-- /.col-lg-8 -->
            <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                <div class="form-group">
                    <div class="panel b_panel">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne"
                                   class="accordion-toggle"><?php ea('page_pages_edit_publish'); ?></a>
                            </h4>
                        </div>
                        <div id="collapseOne" class="panel-collapse collapse in" style="">
                            <div class="panel-body">
                                <?php ea('page_pages_edit_info'); ?>
                                <hr>
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="form-group">
                                            <label><?php ea('page_pages_new_status_label'); ?></label>
                                            <input type="hidden" name="pagePUBLIC" id="pagePUBLIC"
                                                   value="<?php if (isset($pagePUBLIC)) {
                                                       echo $pagePUBLIC;
                                                   } else {
                                                       echo '1';
                                                   } ?>">
                                            <span id="pagePUBLICLabel"
                                                  class="text-capitalize"><?php if (isset($pagePUBLIC) && isset($pagePUBLICLabel)) {
                                                    echo $pagePUBLICLabel;
                                                } else {
                                                    ea('page_pages_status_publish');
                                                } ?></span>
                                            <a href="#pagePUBLIC" id="editPagePUBLICButton" style="display: inline;">
                                                <span aria-hidden="true">- <?php ea('page_pages_new_label_edit_status'); ?></span></a>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 col-md-12 col-sm-12 hidden" id="pagePUBLICEdit"
                                         style="display: block;">
                                        <div class="form-group">
                                            <select name="pagePUBLICselect" id="pagePUBLICselect" class="form-control"
                                                    style="display: inline-block; width: auto">
                                                <option selected="selected"
                                                        value="1"><?php ea('page_pages_status_publish'); ?></option>
                                                <option value="0"><?php ea('page_pages_status_draft'); ?></option>
                                            </select>
                                            <a href="#pagePUBLICselect" class="btn btn-default"
                                               id="okPagePUBLICButton"><?php ea('page_pages_new_label_ok'); ?></a>
                                            <a href="#pagePUBLICselect"
                                               id="cancelPagePUBLICButton"> <?php ea('page_pages_new_label_cancel'); ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                            <button type="submit" name="pages_new_create"
                                    class="btn btn-primary b_btn btn-block"><?php ea('page_pages_edit_publish_button'); ?></button>
                    </div>
                </div>
            </div>

        </div>
    </form>
    <!-- /.row -->
</div>
<!-- /#page-wrapper -->

</div>
<!-- /#wrapper -->


</body>

</html>
<?php $this->container['plugins']->applyEvent('myPageEditBeforeFooter'); ?>
<?php $this->container['plugins']->applyEvent('myPageNewEditBeforeFooter'); ?>
