<?php
/*                     *\
|	MYCMS - TProgram    |
\*                     */

$this->container['users']->hideIfStaffNotLogged();

define('PAGE_ID', 'admin_posts_edit');
define('PAGE_NAME', $this->container['languages']->ea('admin_posts_edit', '1'));

$this->container['theme']->addStyleScriptAdmin('script', '{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/tinymce/tinymce.min.js');

$this->getFileAdmin('header');
$this->getPageAdmin('topbar');

$this->container['plugins']->applyEvent('postsEditAfterTopBar');
$this->container['plugins']->applyEvent('postsNewEditAfterTopBar');

$this->container['plugins']->addEvent('parsePostContent', function ($content) {
    return $content;
});

$info = "";
if (isset($_GET['id'])) {

    if (is_numeric($_GET['id'])) {
        if ($this->container['database']->single("SELECT count(*) FROM my_blog WHERE postID = '" . $_GET['id'] . "' LIMIT 1") > 0) {
            $postid = $this->container['security']->mySqlSecure($_GET['id']);

            $posts['title'] = $this->container['blog']->gets('title', $postid);
            $posts['content'] = $this->container['blog']->gets('content', $postid);
            $posts['permalink'] = $this->container['blog']->gets('permalink', $postid);
            $posts_category = $this->container['blog']->gets('category', $postid);
            $postSTATUS = $this->container['blog']->getInfo('postSTATUS', $postid);
            $postSTATUSLabel = ($postSTATUS == "publish") ? $this->container['languages']->ea('page_posts_new_label_published', '1') : (($postSTATUS == "pending") ? $this->container['languages']->ea('page_posts_new_label_pending_review', '1') : $this->container['languages']->ea('page_posts_new_label_draft', '1'));

        }
    } else {
        header('Location: ' . HOST . '/my-admin/home');
        exit();
    }

} else {

    header('Location: ' . HOST . '/my-admin/home');
    exit();

}


if (isset($_POST['posts_new_edit_button'])) {
    if (!empty($_POST['posts_title'])) {
        if (!empty($_POST['posts_content'])) {
            $user_rank = $this->container['users']->getInfo($_SESSION['staff']['id'], 'rank');
            if ($user_rank >= 2) {
                $posts_title = addslashes($_POST['posts_title']);
                $posts_content = $this->container['plugins']->applyEvent('parsePostContent', $_POST['posts_content']);
                $date = date('d/m/Y H.i.s', time());
                $postSTATUS = addslashes($_POST['postSTATUS']);
                $category = addslashes($_POST['category']);
                //$author = $this->container['users']->getInfo($_SESSION['staff']['id'], 'name').'_'.$this->container['users']->getInfo($_SESSION['staff']['id'], 'surname');
                $author = $_SESSION['staff']['id'];
                //$permalink = $_POST['permalink'];
                $permalink = '/blog/' . date('Y', time()) . '/' . date('m', time()) . '/' . $this->container['functions']->fixText($this->container['functions']->addSpace($posts_title));
                $finder = $this->container['blog']->permalinkFinder($permalink);
                if ($finder == true) {

                    $i = 1;
                    while ($this->container['blog']->permalinkFinder($permalink . '_' . $i) == true):

                        $i++;

                    endwhile;

                    $permalink = $permalink . '_' . $i;

                }

                $this->container['database']->query("UPDATE my_blog SET postTITLE = '$posts_title', postCONT = '$posts_content', postCATEGORY = '$category', postPOSTED = '1', postPERMALINK = '$permalink', postSTATUS = '$postSTATUS' WHERE postID = '" . $postid . "'");;

                $info = '<div class="row"><div class="alert alert-success">' . $this->container['languages']->ea('page_posts_edit_new_success_posted', '1') . ' <a href="' . $permalink . '">' . $this->container['languages']->ea('page_posts_edit_new_success_show', '1') . '</a></div>';

                $posts['title'] = $_POST['posts_title'];
                $posts['content'] = $this->container['plugins']->applyEvent('parsePostContent', $_POST['posts_content']);
                $posts_category = $_POST['category'];
            }
        } else {
            define("INDEX_ERROR", $this->container['languages']->ea('page_posts_edit_new_error_content', '1'));
            $posts['title'] = $_POST['posts_title'];
            $posts['content'] = $this->container['plugins']->applyEvent('parsePostContent', $_POST['posts_content']);
            $posts_category = $_POST['category'];
            $postSTATUS = addslashes($_POST['postSTATUS']);
            $postSTATUSLabel = ($postSTATUS == "publish") ? $this->container['languages']->ea('page_posts_new_label_published', '1') : (($postSTATUS == "pending") ? $this->container['languages']->ea('page_posts_new_label_pending_review', '1') : $this->container['languages']->ea('page_posts_new_label_draft', '1'));


        }
    } else {
        $posts['content'] = $this->container['plugins']->applyEvent('parsePostContent', $_POST['posts_content']);
        $posts_category = $_POST['category'];
        define("INDEX_ERROR", $this->container['languages']->ea('page_posts_edit_new_error_title', '1'));
        $postSTATUS = addslashes($_POST['postSTATUS']);
        $postSTATUSLabel = ($postSTATUS == "publish") ? $this->container['languages']->ea('page_posts_new_label_published', '1') : (($postSTATUS == "pending") ? $this->container['languages']->ea('page_posts_new_label_pending_review', '1') : $this->container['languages']->ea('page_posts_new_label_draft', '1'));


    }
}
$this->getStyleScriptAdmin('script');
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
            <h1 class="h1PagesTitle"><?php $this->container['languages']->ea('page_posts_edit_new_header'); ?></h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <form role="form" method="post" action="">
        <div class="row">
            <div class="col-lg-8 col-md-8">
                <div class="panel b_panel">
                    <div class="panel-body b_panel-body panel-body-padding">
                        <div class="form-group">
                            {@noTAGS_start@}
                            <input placeholder="<?php $this->container['languages']->ea('page_posts_edit_new_title'); ?>" type="text"
                                   name="posts_title" id="title" class="form-control b_form-control" maxlength="100"
                                   value="<?php echo $posts['title']; ?>">
                            {@noTAGS_end@}
                        </div>
                        <br/>
                        <div class="addons-menu">
                            <?php $this->container['plugins']->applyEvent('blogAddonsMenu'); ?>
                        </div>
                        <br/>
                        <div class="form-group">
                            {@noTAGS_start@}
                            <textarea name="posts_content"
                                      style="height:300px;"><?php echo $posts['content']; ?></textarea>
                            {@noTAGS_end@}
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.col-lg-8 -->
            <div class="col-lg-4 col-md-4">
                <div class="form-group">
                    <div class="panel b_panel">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne"
                                   class="accordion-toggle"><?php $this->container['languages']->ea('page_posts_edit_new_publish'); ?></a>
                            </h4>
                        </div>
                        <div id="collapseOne" class="panel-collapse collapse in" style="">
                            <div class="panel-body b_panel-body">
                                <div class="panel-body-padding">
                                    <span class="label label-danger"><?php $this->container['languages']->ea('page_posts_edit_new_permalink'); ?></span><br/>
                                    <p id="msg" style="word-wrap: break-word; ">
                                        {@siteURL@}/blog/<?php echo date('Y', time()); ?>
                                        /<?php echo date('m', time()); ?>
                                        /<?php echo $posts['title']; ?></p>
                                    <small>*<?php $this->container['languages']->ea('page_posts_edit_new_permalink_info'); ?></small>

                                    <hr>
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="form-group">
                                                <label><?php $this->container['languages']->ea('page_posts_new_status_label'); ?></label>
                                                <input type="hidden" name="postSTATUS" id="postSTATUS"
                                                       value="<?php if (isset($postSTATUS)) {
                                                           echo $postSTATUS;
                                                       } else {
                                                           echo 'published';
                                                       } ?>">
                                                <span id="postSTATUSLabel"
                                                      class="text-capitalize"><?php if (isset($postSTATUS)) {
                                                        echo $postSTATUSLabel;
                                                    } else {
                                                        $this->container['languages']->ea('page_posts_new_label_published');
                                                    } ?></span>
                                                <a href="#postSTATUS" id="editPostStatusButton"
                                                   style="display: inline;">
                                                    <span aria-hidden="true">- <?php $this->container['languages']->ea('page_posts_new_label_edit_status'); ?></span></a>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 col-md-12 col-sm-12 hidden" id="postStatusEdit"
                                             style="display: block;">
                                            <div class="form-group">
                                                <select name="postSTATUSselect" id="postSTATUSselect"
                                                        class="form-control"
                                                        style="display: inline-block; width: auto">
                                                    <option selected="selected"
                                                            value="publish"><?php $this->container['languages']->ea('page_posts_new_label_published'); ?></option>
                                                    <option value="pending"><?php $this->container['languages']->ea('page_posts_new_label_pending_review'); ?></option>
                                                    <option value="draft"><?php $this->container['languages']->ea('page_posts_new_label_draft'); ?></option>
                                                </select>
                                                <a href="#postSTATUSselect" class="btn btn-default"
                                                   id="okPostStatusButton"><?php $this->container['languages']->ea('page_posts_new_label_ok'); ?></a>
                                                <a href="#postSTATUSselect"
                                                   id="cancelPostStatusButton"> <?php $this->container['languages']->ea('page_posts_new_label_cancel'); ?></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" name="posts_new_edit_button"
                                class="btn btn-primary b_btn btn-block"><?php $this->container['languages']->ea('page_posts_edit_new_publish_button'); ?></button>

                    </div>
                </div>
                <div class="form-group">
                    <div class="panel b_panel">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo"
                                   class="accordion-toggle"><?php $this->container['languages']->ea('page_posts_edit_new_category'); ?></a>
                            </h4>
                        </div>
                        <div id="collapseTwo" class="panel-collapse collapse in" style="">
                            <div class="panel-body">
                                <span class="label label-warning"><?php $this->container['languages']->ea('page_posts_edit_new_select_category'); ?></span><br/><br/>
                                <select name="category" id="categorySelect" class="form-control b_form-control">
                                    <?php
                                    $cat = $this->container['database']->query("SELECT * FROM my_blog_category");
                                    $i = 0;
                                    foreach ($cat as $category) {
                                        $i++;
                                        ?>
                                        <option <?php if ($posts_category == $category['catNAME']) {
                                            echo 'selected=""';
                                        } ?> value="<?php echo $category['catNAME']; ?>"><?php echo $category['catNAME']; ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                                <br>
                                <a href="#category-add"
                                   id="category-add-button"><?php $this->container['languages']->ea('page_posts_new_category_button'); ?></a>
                                <div id="category-add" class="hidden">
                                    <div class="row">
                                        <hr>
                                        <div class="col-lg-12 col-md-12 col-sm-6 col-xs-6">
                                            <input class="form-control b_form-control" type="text" aria-required="true"
                                                   name="newCategoryName" id="newCategoryName"
                                                   style="margin-bottom: 10px"
                                                   placeholder="<?php $this->container['languages']->ea('page_posts_category_new_placeholder'); ?>">
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-6 col-xs-6">
                                            <a class="btn btn-primary b_btn btn-block" name="addNewCategoryName"
                                               id="addNewCategoryName"><?php $this->container['languages']->ea('page_posts_category_new_button'); ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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


<script type="text/javascript">
    $("#title").keyup(function () {

        var text = '{@siteURL@}/blog/<?php echo date('Y', time());?>/<?php echo date('m', time());?>/';

        var replaced = $("#title").val();


        $('#msg').html(text += replaced.replace(/\s/g, '_'));

    });

</script>
</body>

</html>
<?php $this->container['plugins']->applyEvent('postsEditBeforeFooter'); ?>
<?php $this->container['plugins']->applyEvent('postsNewEditBeforeFooter'); ?>

