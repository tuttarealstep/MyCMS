<?php
/*                     *\
|	MyCMS    |
\*                     */

$this->container['users']->hideIfNotLogged();

if(!$this->container['users']->currentUserHasPermission("edit_pages"))
{
    throw new MyCMS\App\Utils\Exceptions\MyCMSException("You do not have permission to access this page!", "Permission denied");
}

define('PAGE_ID', 'admin_pages_edit');
define('PAGE_NAME', $this->container['languages']->ta('page_pages_edit', true));

$this->container['theme']->addStyleScriptAdmin('script', '{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/tinymce/tinymce.min.js');

$this->getFileAdmin('header');
$this->getPageAdmin('topbar');
$this->container['plugins']->applyEvent('myPageEditAfterTopBar');
$this->container['plugins']->applyEvent('myPageNewEditAfterTopBar');


if (isset($_GET['id'])) {

    if (is_numeric($_GET['id'])) {
        if ($this->container['database']->single("SELECT count(*) FROM my_page WHERE pageId = '" . $_GET['id'] . "' LIMIT 1") > 0) {
            $pageid = $this->container['security']->mySqlSecure($_GET['id']);
            $pages['id'] = $pageid;
            $pages['title'] = $this->container['functions']->removeSpace($this->container['database']->single("SELECT pageTitle FROM my_page WHERE pageId = '" . $_GET['id'] . "' LIMIT 1"));
            $pages['content'] = $this->container['database']->single("SELECT pageHtml FROM my_page WHERE pageId = '" . $_GET['id'] . "' LIMIT 1");
            $pages['URL'] = $this->container['database']->single("SELECT pageUrl FROM my_page WHERE pageId = '" . $_GET['id'] . "' LIMIT 1");
            $pagePublic = $this->container['database']->single("SELECT pagePublic FROM my_page WHERE pageId = '" . $_GET['id'] . "' LIMIT 1");
            $pages['customCSS'] = $this->container['database']->single("SELECT pageCustomCss FROM my_page WHERE pageId = '" . $_GET['id'] . "' LIMIT 1");

            if($pagePublic == "1")
            {
                if(!$this->container['users']->currentUserHasPermission("edit_published_pages"))
                {

                    header('Location: ' . HOST . '/my-admin/home');
                    exit();
                }
            } else {
                if(!$this->container['users']->currentUserHasPermission("edit_private_pages"))
                {

                    header('Location: ' . HOST . '/my-admin/home');
                    exit();
                }
            }

            $pagePublicLabel = ($pagePublic == "1") ? $this->container['languages']->ea('page_pages_status_publish', '1') : $this->container['languages']->ea('page_pages_status_draft', '1');

        }
    } else {
        header('Location: ' . HOST . '/my-admin/home');
        exit();
    }

} else {

    header('Location: ' . HOST . '/my-admin/home');
    exit();

}

$this->container['plugins']->addEvent('parseMyPageContent', function ($content) {
    return $content;
});


if (isset($_POST['pages_new_create'])) {
    if (!empty($_POST['pages_title'])) {
        $pages_title = $this->container['functions']->addSpace(addslashes($_POST['pages_title']));
        $pages_content = $this->container['plugins']->applyEvent('parseMyPageContent', $_POST['pages_content']);


        $pagePublic = addslashes($_POST['pagePublic']);
        $pagePublicLabel = ($pagePublic == "1") ? $this->container['languages']->ea('page_pages_status_publish', '1') : $this->container['languages']->ea('page_pages_status_draft', '1');


        $pages_menu_id = $this->container['security']->myGenerateRandom(5) . $pages_title;

        $this->container['database']->query("UPDATE my_page SET pageTitle = '$pages_title', pagePublic = '$pagePublic', pageHtml = '$pages_content'WHERE pageId = '" . $pageid . "'");;
        $info = '<div class="row"><div class="alert alert-success">' . $this->container['languages']->ea('page_pages_edit_success_created', '1') . ' <a href="' . $pages['URL'] . '">' . $this->container['languages']->ea('page_pages_edit_success_show', '1') . '</a></div>';
        $pages['title'] = $_POST['pages_title'];
        $pages['content'] = $this->container['plugins']->applyEvent('parseMyPageContent', $_POST['pages_content']);

        $this->container['plugins']->applyEvent('myPageEditSaveSuccess', $pages, $_POST);
    } else {
        $pagePublic = addslashes($_POST['pagePublic']);
        $pagePublicLabel = ($pagePublic == "1") ? $this->container['languages']->ea('page_pages_status_publish', '1') : $this->container['languages']->ea('page_pages_status_draft', '1');

        $pages['content'] = $this->container['plugins']->applyEvent('parseMyPageContent', $_POST['pages_content']);
        define("INDEX_ERROR", $this->container['languages']->ea('page_pages_edit_error_title', '1'));

    }
}
$this->getStyleScriptAdmin('script');
?>
<?php $this->container['plugins']->applyEvent('myPageEditAfterHeader'); ?>
<?php $this->container['plugins']->applyEvent('myPageNewEditAfterHeader'); ?>
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
        autosave_ask_before_unload: false,
        relative_urls : false,
        remove_script_host: false
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
<div class="container" id="containerElements">
    <div class="row">
        <div class="col-lg-12">
            <?php if (!empty($info)) {
                echo '<br>' . $info . '<br>';
            } ?>
            <h1 class="h1PagesTitle"><?php $this->container['languages']->ea('page_pages_edit_header'); ?></h1>
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
                            <input type="text" name="pages_title" id="title" class="form-control b_form-control"
                                   maxlength="100"
                                   value="<?php echo $pages['title']; ?>"
                                   placeholder="<?php $this->container['languages']->ea('page_pages_edit_title'); ?>">
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
                                      style="height:300px;"><?php echo htmlentities($pages['content']); ?></textarea>
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
                                   class="accordion-toggle"><?php $this->container['languages']->ea('page_pages_edit_publish'); ?></a>
                            </h4>
                        </div>
                        <div id="collapseOne" class="panel-collapse collapse in" style="">
                            <div class="panel-body">
                                <?php $this->container['languages']->ea('page_pages_edit_info'); ?>
                                <hr>
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="form-group">
                                            <label><?php $this->container['languages']->ea('page_pages_new_status_label'); ?></label>
                                            <input type="hidden" name="pagePublic" id="pagePublic"
                                                   value="<?php if (isset($pagePublic)) {
                                                       echo $pagePublic;
                                                   } else {
                                                       echo '1';
                                                   } ?>">
                                            <span id="pagePublicLabel"
                                                  class="text-capitalize"><?php if (isset($pagePublic) && isset($pagePublicLabel)) {
                                                    echo $pagePublicLabel;
                                                } else {
                                                    $this->container['languages']->ea('page_pages_status_publish');
                                                } ?></span>
                                            <a href="#pagePublic" id="editPagePUBLICButton" style="display: inline;">
                                                <span aria-hidden="true">- <?php $this->container['languages']->ea('page_pages_new_label_edit_status'); ?></span></a>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 col-md-12 col-sm-12 hidden" id="pagePublicEdit"
                                         style="display: block;">
                                        <div class="form-group">
                                            <select name="pagePublicselect" id="pagePublicselect" class="form-control"
                                                    style="display: inline-block; width: auto">
                                                <option selected="selected"
                                                        value="1"><?php $this->container['languages']->ea('page_pages_status_publish'); ?></option>
                                                <option value="0"><?php $this->container['languages']->ea('page_pages_status_draft'); ?></option>
                                            </select>
                                            <a href="#pagePublicselect" class="btn btn-default"
                                               id="okPagePUBLICButton"><?php $this->container['languages']->ea('page_pages_new_label_ok'); ?></a>
                                            <a href="#pagePublicselect"
                                               id="cancelPagePUBLICButton"> <?php $this->container['languages']->ea('page_pages_new_label_cancel'); ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" name="pages_new_create"
                                class="btn btn-primary b_btn btn-block"><?php $this->container['languages']->ea('page_pages_edit_publish_button'); ?></button>
                    </div>
                </div>
            </div>
            <input type="hidden" id="customCSSInput" name="customCSSInput" value="<?php echo htmlentities($pages['customCSS']); ?>"/>
            <?php $this->container['plugins']->applyEvent('myPageEditInsideForm'); ?>
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

