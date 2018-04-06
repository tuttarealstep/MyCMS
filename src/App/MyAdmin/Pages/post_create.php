<?php
//TODO enable/disable comments for single post

$this->container['users']->hideIfStaffNotLogged();

define('PAGE_ID', 'admin_post_create');

if (isset($_GET['id']) && is_numeric($_GET['id']))
{
    define('PAGE_NAME', $this->container['languages']->ta('page_post_create', true));
} else {
    define('PAGE_NAME', $this->container['languages']->ta('page_post_create_edit', true));
}

$this->container['theme']->addStyleScriptAdmin('script', '{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/tinymce/tinymce.min.js');

$this->getFileAdmin('header');
$this->getPageAdmin('topbar');

$this->container['plugins']->applyEvent('postsNewAfterTopBar');
$this->container['plugins']->applyEvent('postsNewEditAfterTopBar');

$this->container['plugins']->addEvent('parsePostContent', function ($content) {
    return $content;
});

$this->getStyleScriptAdmin('script');


$postType = "post";
$postTitle = "";
$postAuthor = $_SESSION['staff']['id'];
$postContent = "";
$postStatus = "draft";
$postCategory = [];
$postName = "";
$postDate = date('Y-m-d H:i', time());
$postDateY = date('Y', time());
$postDateM = date('m', time());
$postDateD = date('d', time());
$postDateH = date('H', time());
$postDateI = date('i', time());
$postDateS = date('s', time());
$postStatusLabel = ($postStatus == "publish") ? $this->container['languages']->ta('page_post_create_label_published', '1') : (($postSTATUS == "pending") ? $this->container['languages']->ta('page_post_create_label_pending_review', true) : $this->container['languages']->ta('page_post_create_label_draft', true));
$commentStatus = "open";

if (isset($_GET['id']) && is_numeric($_GET['id']) && !isset($_POST['post_create_create'])) {

    $postId = (int)$_GET['id'];

    if(!$this->container['blog']->verifyPostId($postId))
    {
        header('Location: ' . HOST . '/my-admin/home');
        exit();
    }

    $postDate = date('Y-m-d H:i', strtotime($this->container['blog']->getInfo('date', $postId)));
    $postType = "post";
    $postTitle = $this->container['blog']->getInfo('title', $postId);
    $postAuthor = $_SESSION['staff']['id'];
    $postContent = $this->container['blog']->getInfo('content', $postId);
    $postStatus = $this->container['blog']->getInfo('postStatus', $postId);
    $postStatusLabel = ($postStatus == "publish") ? $this->container['languages']->ta('page_post_create_label_published', true) : (($postStatus == "pending") ? $this->container['languages']->ta('page_post_create_label_pending_review', true) : $this->container['languages']->ta('page_post_create_label_draft', true));

    $postCategory = $this->container['blog']->getInfo('categoryNameArray', $postId);
    $postName = $this->container['blog']->getInfo('name', $postId);
    $commentStatus = $this->container['blog']->getInfo('commentStatus', $postId);

    if (isset($_GET['createMessage'])) {
        $info = '<div class="row"><div class="alert alert-success">' . $this->container['languages']->ta('page_post_create_success_posted', true) . ' <a href="' . '/blog/' . date('Y', time()) . '/' . date('m', time()) . '/' . $postName . '">' . $this->container['languages']->ta('page_post_create_success_show', true) . '</a></div>';
    }

    if (isset($_GET['postEditSuccess'])) {
        $info = '<div class="row"><div class="alert alert-success">' . $this->container['languages']->ta('page_post_create_edit_new_success_posted', true) . ' <a href="' . '/blog/' . date('Y', time()) . '/' . date('m', time()) . '/' . $postName . '">' . $this->container['languages']->ta('page_post_create_success_show', true) . '</a></div>';
    }
} else {
    if (isset($_POST['post_create_create'])) {

        $postId = (int)$_POST['postId'];
        $postType = "post";
        $postTitle = htmlspecialchars(stripslashes($_POST['postTitle']));
        $postAuthor = $_SESSION['staff']['id'];
        $postContent = $this->container['plugins']->applyEvent('parsePostContent', $_POST['postContent']);
        $postStatus = $_POST['postSTATUS'];
        $postCategory = isset($_POST['category']) ? (array)$_POST['category'] : [];
        $postDate = date('Y-m-d H:i:s', strtotime($_POST['postDate']));

        $postName = (empty($this->container['blog']->getInfo('name', $postId))) ? $this->container['blog']->generateUniqueName($this->container['functions']->fixText($this->container['functions']->addSpace($_POST['postTitle'])), $postId) : $this->container['blog']->getInfo('name', $postId);
        $commentStatus = "open";


        if ($postTitle == "" && $postContent == "")
            return;

        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $postId = (int)$_GET['id'];

            $this->container['database']->query("UPDATE my_blog SET
postTitle = :postTitle,
postContent = :postContent,
postDate = :postDate,
postStatus = :postStatus,
postModified = :postModified,
commentStatus = :commentStatus WHERE postId = :postId", [
                "postTitle"     => $postTitle,
                "postContent"   => $postContent,
                "postDate"      => $postDate,
                "postStatus"    => $postStatus,
                "postModified"  => $postDate = date('Y-m-d H:i:s', time()),
                "commentStatus" => $commentStatus,
                "postId"        => $postId
            ]);

            $this->container['database']->query("DELETE FROM my_blog_category_relationships WHERE postId = :postId", ["postId" => $postId]);

            if (!in_array('noCategory', $postCategory)) {
                foreach ($postCategory as $key => $value) {

                    $this->container['database']->query("INSERT INTO my_blog_category_relationships VALUES (
:postId,
:categoryId)", [
                        "postId"     => $postId,
                        "categoryId" => $this->container['blog']->getCategoryId($value)
                    ]);
                }
            }

            header('Location: ' . HOST . '/my-admin/post_create?id=' . $postId . '&postEditSuccess=true');
            exit();
        } else {
            $this->container['database']->query("UPDATE my_blog SET
postTitle = :postTitle,
postContent = :postContent,
postDate = :postDate,
postAuthor = :postAuthor,
postName = :postName,
postStatus = :postStatus,
postType = :postType,
postModified = :postModified,
commentStatus = :commentStatus WHERE postId = :postId", [
                "postTitle"     => $postTitle,
                "postContent"   => $postContent,
                "postDate"      => $postDate,
                "postAuthor"    => $postAuthor,
                "postName"      => $postName,
                "postStatus"    => $postStatus,
                "postType"      => $postType,
                "postModified"  => $postDate,
                "commentStatus" => $commentStatus,
                "postId"        => $postId
            ]);

        }

        $this->container['database']->query("DELETE FROM my_blog_category_relationships WHERE postId = :postId", ["postId" => $postId]);


        if (!in_array('noCategory', $postCategory)) {
            foreach ($postCategory as $key => $value) {

                $this->container['database']->query("INSERT INTO my_blog_category_relationships VALUES (
:postId,
:categoryId)", [
                    "postId"     => $postId,
                    "categoryId" => $this->container['blog']->getCategoryId($value)
                ]);
            }
        }


        //todo check query errors
        //$info = '<div class="row"><div class="alert alert-success">' . $this->container['languages']->ta('page_post_create_success_posted', true) . ' <a href="'.'/blog/' . date('Y', time()) . '/' . date('m', time()) . '/' . $postName . '">' . $this->container['languages']->ta('page_post_create_success_show', true) . '</a></div>';
        header('Location: ' . HOST . '/my-admin/post_create?id=' . $postId . '&createMessage=true');
        exit();
    } else {
        $this->container['database']->query("INSERT INTO my_blog VALUES (
NULL,
:postTitle,
:postContent,
:postDate,
:postAuthor,
:postName,
:postStatus,
:postType,
:postModified,
:commentStatus)", [
            "postTitle"     => $postTitle,
            "postContent"   => $postContent,
            "postDate"      => $postDate,
            "postAuthor"    => $postAuthor,
            "postName"      => $postName,
            "postStatus"    => $postStatus,
            "postType"      => $postType,
            "postModified"  => $postDate,
            "commentStatus" => $commentStatus,
        ]);

        $postId = $this->container['database']->lastInsertId();
    }
}
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

    .editDateForm > input {
        width: 40px;
        display: inline-block;
        margin-bottom: 4px;
    }

    .editDateForm > select {
        width: 60px;
        display: inline-block;
        margin-bottom: 4px;
    }

    .errorDate {
        border-color: #E53935;
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
            <?php if(isset($_GET['id'])) { ?>
                <h1 class="h1PagesTitle"><?php $this->container['languages']->ta('page_post_create_edit_new_header'); ?></h1>
            <?php } else { ?>
                <h1 class="h1PagesTitle"><?php $this->container['languages']->ta('page_post_create_header'); ?></h1>
            <?php }  ?>
        </div>
    </div>

    <form role="form" method="post" action="">
        <input type="hidden" id="postPage" name="postPage" value="new">
        <input type="hidden" id="postId" name="postId" value="<?php echo $postId; ?>">
        <input type="hidden" id="postDateOriginal" name="postDateOriginal" value="<?php echo $postDate . ":00"; ?>">
        <input type="hidden" id="postDate" name="postDate" value="<?php echo $postDate . ":00"; ?>">
        <div class="row">
            <div class="col-lg-8 col-md-8">
                <div class="panel b_panel">
                    <div class="panel-body b_panel-body panel-body-padding">
                        <div class="form-group">
                            <input type="text"
                                   placeholder="<?php $this->container['languages']->ta('page_post_create_title'); ?>"
                                   name="postTitle"
                                   id="postTitle" class="form-control b_form-control" maxlength="100"
                                   value="<?php echo $postTitle; ?>">
                        </div>
                        <br/>
                        <div class="addons-menu">
                            <?php $this->container['plugins']->applyEvent('blogAddonsMenu'); ?>
                        </div>
                        <br/>
                        <div class="form-group" id="textareaContent">
                            <textarea id="postContent" name="postContent"
                                      style="height:300px;"><?php echo $postContent; ?></textarea>
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
                                   class="accordion-toggle"><?php $this->container['languages']->ta('page_post_create_publish'); ?></a>
                            </h4>
                        </div>
                        <div id="collapseOne" class="panel-collapse collapse in" style="">
                            <div class="panel-body b_panel-body">
                                <div class="panel-body-padding">
                                    <span id="permalinkPanel" class="<?php echo (isset($_GET['id'])) ?: "hidden" ?>">
                                    <span class="label label-danger"><?php $this->container['languages']->ta('page_post_create_permalink'); ?></span><br/>
                                    <p id="permalinkMsg" style="word-wrap: break-word; ">
                                        {@siteURL@}/blog/<?php echo date('Y', time()); ?>
                                        /<?php echo date('m', time()); ?>/<?php echo $postName; ?></p>
                                    <hr>
                                        </span>
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="form-group">
                                                <label><?php $this->container['languages']->ta('page_post_create_status_label'); ?></label>
                                                <input type="hidden" name="postSTATUS" id="postSTATUS"
                                                       value="<?php if (isset($postStatus)) {
                                                           echo $postStatus;
                                                       } else {
                                                           echo 'publish';
                                                       } ?>">
                                                <span id="postSTATUSLabel"
                                                      class="text-capitalize"><?php if (isset($postStatus) && isset($postStatusLabel)) {
                                                        echo $postStatusLabel;
                                                    } else {
                                                        $this->container['languages']->ta('page_post_create_label_published');
                                                    } ?></span>
                                                <a href="#postSTATUS" id="editPostStatusButton"
                                                   style="display: inline;">
                                                    <span aria-hidden="true">- <?php $this->container['languages']->ta('page_post_create_label_edit_status'); ?></span></a>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 col-md-12 col-sm-12 hidden" id="postStatusEdit"
                                             style="display: block;">
                                            <div class="form-group">
                                                <select name="postSTATUSselect" id="postSTATUSselect"
                                                        class="form-control"
                                                        style="display: inline-block; width: auto">
                                                    <option
                                                        <?php if ($postStatus == "publish") { ?>selected="selected" <?php } ?>
                                                        value="publish"><?php $this->container['languages']->ta('page_post_create_label_published'); ?></option>
                                                    <option
                                                        <?php if ($postStatus == "pending") { ?>selected="selected" <?php } ?>
                                                        value="pending"><?php $this->container['languages']->ta('page_post_create_label_pending_review'); ?></option>
                                                    <option
                                                        <?php if ($postStatus == "draft") { ?>selected="selected" <?php } ?>
                                                        value="draft"><?php $this->container['languages']->ta('page_post_create_label_draft'); ?></option>
                                                </select>
                                                <a href="#postSTATUSselect" class="btn btn-default"
                                                   id="okPostStatusButton"><?php $this->container['languages']->ta('page_post_create_label_ok'); ?></a>
                                                <a href="#postSTATUSselect"
                                                   id="cancelPostStatusButton"> <?php $this->container['languages']->ta('page_post_create_label_cancel'); ?></a>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="form-group">
                                                <label><?php $this->container['languages']->ta('page_post_create_date_label'); ?></label>
                                                <span id="postDateLabel"
                                                      class="<?php if (strtotime(date('Y-m-d H:i', time()) . ":00") != strtotime($postDate)) {
                                                          echo "hidden";
                                                      } ?>">
                                                    <?php $this->container['languages']->ta('page_post_create_date_now'); ?>
                                                </span>
                                                <span id="postDatePlanned"
                                                      class="<?php if (strtotime(date('Y-m-d H:i', time()) . ":00") == strtotime($postDate)) {
                                                          echo "hidden";
                                                      } ?>">
                                                    <small><?php $this->container['languages']->ta('page_post_create_date_planned'); ?>
                                                        (<span id="postDatePlannedLabel"
                                                               class="text-capitalize"> <b><?php echo $postDate; ?></b></span>)</small>
                                                </span>
                                                <a href="#postDateEdit" id="editPostDateButton"
                                                   style="display: inline;">
                                                    <span aria-hidden="true">- <?php $this->container['languages']->ta('page_post_create_label_edit_date'); ?></span></a>
                                                <br>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 col-md-12 col-sm-12 hidden" id="postDateEdit"
                                             style="display: block;">
                                            <div class="form-group editDateForm">
                                                <input type="text" class="form-control b_form-control" name="dateD"
                                                       id="dateD" value="<?php echo $postDateD; ?>">
                                                <select class="form-control b_form-control" id="dateM" name="dateM">
                                                    <option value="01" <?php if ($postDateM == "01") {
                                                        echo "selected=\"selected\" ";
                                                    } ?>>01
                                                    </option>
                                                    <option value="02" <?php if ($postDateM == "02") {
                                                        echo "selected=\"selected\" ";
                                                    } ?>>02
                                                    </option>
                                                    <option value="03" <?php if ($postDateM == "03") {
                                                        echo "selected=\"selected\" ";
                                                    } ?>>03
                                                    </option>
                                                    <option value="04" <?php if ($postDateM == "04") {
                                                        echo "selected=\"selected\" ";
                                                    } ?>>04
                                                    </option>
                                                    <option value="05" <?php if ($postDateM == "05") {
                                                        echo "selected=\"selected\" ";
                                                    } ?>>05
                                                    </option>
                                                    <option value="06" <?php if ($postDateM == "06") {
                                                        echo "selected=\"selected\" ";
                                                    } ?>>06
                                                    </option>
                                                    <option value="07" <?php if ($postDateM == "07") {
                                                        echo "selected=\"selected\" ";
                                                    } ?>>07
                                                    </option>
                                                    <option value="08" <?php if ($postDateM == "08") {
                                                        echo "selected=\"selected\" ";
                                                    } ?>>08
                                                    </option>
                                                    <option value="09" <?php if ($postDateM == "09") {
                                                        echo "selected=\"selected\" ";
                                                    } ?>>09
                                                    </option>
                                                    <option value="10" <?php if ($postDateM == "10") {
                                                        echo "selected=\"selected\" ";
                                                    } ?>>10
                                                    </option>
                                                    <option value="11" <?php if ($postDateM == "11") {
                                                        echo "selected=\"selected\" ";
                                                    } ?>>11
                                                    </option>
                                                    <option value="12" <?php if ($postDateM == "12") {
                                                        echo "selected=\"selected\" ";
                                                    } ?>>12
                                                    </option>
                                                </select>
                                                <input type="text" class="form-control b_form-control"
                                                       style="width: 60px" name="dateY" id="dateY"
                                                       value="<?php echo $postDateY; ?>"><?php $this->container['languages']->ta('page_post_create_label_edit_date_at'); ?>
                                                <input type="text" class="form-control b_form-control" name="dateH"
                                                       id="dateH" value="<?php echo $postDateH; ?>">
                                                <input type="text" class="form-control b_form-control" name="dateI"
                                                       id="dateI" value="<?php echo $postDateI; ?>">
                                                <br>
                                                <a href="#postDateEdit" class="btn btn-default"
                                                   id="okPostDateButton"><?php $this->container['languages']->ta('page_post_create_label_ok'); ?></a>
                                                <a href="#postDateEdit"
                                                   id="cancelPostDateButton"> <?php $this->container['languages']->ta('page_post_create_label_cancel'); ?></a>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" name="post_create_create" id="post_create_create"
                                class="btn btn-primary b_btn btn-block"><?php $this->container['languages']->ta('page_post_create_publish_button'); ?></button>

                    </div>
                </div>
                <div class="form-group">
                    <div class="panel b_panel">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo"
                                   class="accordion-toggle"><?php $this->container['languages']->ta('page_post_create_category'); ?></a>
                            </h4>
                        </div>
                        <div id="collapseTwo" class="panel-collapse collapse in" style="">
                            <div class="panel-body b_panel-body">
                                <div class="panel-body-padding">
                                    <span class="label label-warning"><?php $this->container['languages']->ta('page_post_create_select_category'); ?></span><br/><br/>
                                    <select name="category[]" id="categorySelect" class="form-control b_form-control"
                                            multiple>
                                        <option <?php if (in_array("noCategory", $postCategory)) {
                                            echo 'selected=""';
                                        } ?> value="noCategory"><?php $this->container['languages']->ta('page_post_create_select_option_no_category'); ?></option>
                                        <?php
                                        $cat = $this->container['database']->query("SELECT * FROM my_blog_category");
                                        $i = 0;
                                        foreach ($cat as $category) {
                                            $i++;
                                            ?>
                                            <option <?php if (in_array($category['categoryName'], $postCategory)) {
                                                echo 'selected=""';
                                            } ?> value="<?php echo $category['categoryName']; ?>"><?php echo $category['categoryName']; ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                    <br>
                                    <a href="#category-add"
                                       id="category-add-button"><?php $this->container['languages']->ta('page_post_create_category_button'); ?></a>
                                    <div id="category-add" class="hidden">
                                        <div class="row">
                                            <hr>
                                            <div class="col-lg-12 col-md-12 col-sm-6 col-xs-6">
                                                <input class="form-control b_form-control" type="text"
                                                       aria-required="true"
                                                       name="newCategoryName" id="newCategoryName"
                                                       style="margin-bottom: 10px"
                                                       placeholder="<?php $this->container['languages']->ta('page_posts_category_new_placeholder'); ?>">
                                            </div>
                                            <div class="col-lg-12 col-md-12 col-sm-6 col-xs-6">
                                                <a class="btn btn-primary b_btn btn-block" name="addNewCategoryName"
                                                   id="addNewCategoryName"><?php $this->container['languages']->ta('page_posts_category_new_button'); ?></a>
                                            </div>
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
</div>

<?php $this->container['plugins']->applyEvent('postsNewBeforeFooter'); ?>
<?php $this->container['plugins']->applyEvent('postsNewEditBeforeFooter'); ?>
