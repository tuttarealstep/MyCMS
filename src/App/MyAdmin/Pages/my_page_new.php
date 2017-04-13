<?php
    /*                     *\
    |	MYCMS - TProgram    |
    \*                     */



hide_if_staff_not_logged();
$user_rank = $this->container["users"]->getInfo($_SESSION['staff']['id'], 'rank');
if ($user_rank < 3)
{
    header('Location: ' . HOST . '/my-admin/home');
    exit();
}

    global $my_db, $my_users, $my_blog, $my_theme;

    define('PAGE_ID', 'admin_pages_new');
    define('PAGE_NAME', ea('page_pages_new', '1'));

    add_style_script_admin('script', '{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/tinymce/tinymce.min.js');

    get_file_admin('header');

    get_page_admin('topbar');
$this->container['plugins']->applyEvent('myPageNewAfterTopBar');
$this->container['plugins']->applyEvent('myPageNewEditAfterTopBar');

$this->container['plugins']->addEvent('parseMyPageContent', function ($content)
{
    return $content;
});

    if (isset($_POST['pages_new_create'])) {
        if (!empty($_POST['pages_title'])) {
            $pages_title = add_space(addslashes($_POST['pages_title']));

            $pages_content = $this->container['plugins']->applyEvent('parseMyPageContent', $_POST['pages_content']);

            $pagePUBLIC = addslashes($_POST['pagePUBLIC']);

            $pages_menu_id = my_generate_random(5) . $pages_title;

            $pageUrlFromTitle = preg_replace('/[^\p{L}\p{N}\s]/u', '', $pages_title);

            $page_url = "{@siteURL@}/" . $this->container["security"]->my_sql_secure($pageUrlFromTitle);


            $find_url = $my_db->single("SELECT COUNT(*) FROM my_page WHERE pageURL = :simulate_url", array("simulate_url" => $page_url));
            if ($find_url > 0) {
                $page_url = "{@siteURL@}/" . my_generate_random(5) . $this->container["security"]->my_sql_secure($pageUrlFromTitle);
            } else {
            }


            $my_db->query("INSERT INTO my_page (pageTITLE,pageURL,pagePUBLIC,pageHTML, pageID_MENU) VALUES ('$pages_title', '$page_url', '$pagePUBLIC', '$pages_content', '$pages_menu_id')");
            $info = '<div class="row"><div class="alert alert-success">' . ea('page_pages_new_success_created', '1') . ' <a href="' . $page_url . '">' . ea('page_pages_new_success_show', '1') . '</a></div>';

        } else {
            $pagePUBLIC = addslashes($_POST['pagePUBLIC']);
            $pagePUBLICLabel = ($pagePUBLIC == "1") ? ea('page_pages_status_publish', '1') : ea('page_pages_status_draft', '1');

            $pages['content'] = $this->container['plugins']->applyEvent('parseMyPageContent', $_POST['pages_content']);
            define("INDEX_ERROR", ea('page_pages_new_error_title', '1'));

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
            <h1 class="h1PagesTitle"><?php ea('page_pages_new_header'); ?></h1>
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
                            <input type="text" name="pages_title" id="title" class="form-control b_form-control"
                                   maxlength="100" placeholder="<?php ea('page_pages_new_title'); ?>"
                                   value="<?php echo (isset($pages['title'])) ? $pages['title'] : ""; ?>">
                        </div>
                        <br/>
                        <div class="addons-menu">
                            <?php $this->container['plugins']->applyEvent('myPageAddonsMenu'); ?>
                        </div>
                        <br/>
                        <div class="form-group" id="textareaContent">
                            <textarea name="pages_content" id="pages_content"
                                      style="height:300px;"><?php echo $my_theme->no_tags((isset($pages['content'])) ? $pages['content'] : ""); ?></textarea>
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
                                   class="accordion-toggle"><?php ea('page_pages_new_publish'); ?></a>
                            </h4>
                        </div>
                        <div id="collapseOne" class="panel-collapse collapse in" style="">
                            <div class="panel-body b_panel-body">
                                <div class="panel-body-padding">
                                    <?php ea('page_pages_new_info'); ?>
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
                        </div>
                        <button type="submit" name="pages_new_create" id="pages_new_create"
                                class="btn  btn-primary b_btn btn-block"><?php ea('page_pages_new_publish_button'); ?></button>

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
<?php $this->container['plugins']->applyEvent('myPageNewBeforeFooter'); ?>
<?php $this->container['plugins']->applyEvent('myPageNewEditBeforeFooter'); ?>
