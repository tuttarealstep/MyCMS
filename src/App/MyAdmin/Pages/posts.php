<?php
/*                     *\
|	MYCMS - TProgram    |
\*                     */
$this->container['users']->hideIfNotLogged();

if(!$this->container['users']->currentUserHasPermission("read_private_posts"))
{
    throw new MyCMS\App\Utils\Exceptions\MyCMSException("You do not have permission to access this page!", "Permission denied");
}

define('PAGE_ID', 'admin_posts');
define('PAGE_NAME', $this->container['languages']->ta('page_posts_name', true));

$this->getFileAdmin('header');
$this->getPageAdmin('topbar');

$info = "";

function deletePost($id)
{
    $this->container['database']->query('DELETE FROM my_blog WHERE postId = :select', ['select' => $id]);
}

if (isset($_POST['execute'])) {
        if ($_POST['ifchecked'] == 'delete') {
            if (!empty($_POST['check_list'])) {
                foreach ($_POST['check_list'] as $select)
                {
                    if ($this->container['users']->currentUserHasPermission("delete_posts"))
                    {
                        deletePost($select);
                        $info = '<div class="row"><div class="alert alert-success">' . $this->container['languages']->ta('page_posts_delete_successfull', true) . '</div>';
                    } else {
                        if ($this->container['database']->single('SELECT postAuthor FROM my_blog WHERE postId = :select', ['select' => $select]) == $_SESSION['user']['id']) {
                            if ($this->container['database']->single('SELECT postStatus FROM my_blog WHERE postId = :select', ['select' => $select]) == "publish") {
                                if ($this->container['users']->currentUserHasPermission("delete_published_posts")) {
                                    deletePost($select);
                                    $info = '<div class="row"><div class="alert alert-success">' . $this->container['languages']->ta('page_posts_delete_successfull', true) . '</div>';
                                }
                            } else {
                                if ($this->container['users']->currentUserHasPermission("delete_private_posts")) {
                                    deletePost($select);
                                    $info = '<div class="row"><div class="alert alert-success">' . $this->container['languages']->ta('page_posts_delete_successfull', true) . '</div>';
                                }
                            }
                        } else {
                            if ($this->container['users']->currentUserHasPermission("delete_others_posts")) {
                                deletePost($select);
                                $info = '<div class="row"><div class="alert alert-success">' . $this->container['languages']->ta('page_posts_delete_successfull', true) . '</div>';
                            }
                        }
                    }
                }
            } else {
                $info = '<div class="row"><div class="alert alert-danger">' . $this->container['languages']->ta('page_posts_delete_empty_checklist', true) . '</div>';
            }
        } elseif ($_POST['ifchecked'] == 'edit') {
            if (!empty($_POST['check_list'])) {
                foreach ($_POST['check_list'] as $select) {
                    header('Location: ' . HOST . '/my-admin/posts_edit/' . $select . '');
                    exit();
                }
            }
        }
}
?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <?php if (!empty($info)) {
                echo '<br>' . $info . '<br>';
            } ?>
            <?php
            if($this->container['users']->currentUserHasPermission("publish_posts"))
            {
            ?>
            <h1 class="h1PagesTitle"><?php $this->container['languages']->ta('page_posts_header'); ?> <a href="{@siteURL@}/my-admin/post_create"
                                                                          class="btn btn-primary pull-right"><?php $this->container['languages']->ta('page_posts_header_create_new'); ?></a>
            </h1>
            <?php } else { ?>
                <br>
            <?php } ?>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div>
                <form action="" method="post">
                    <table class="table table-striped table-bordered table-hover" id="tables_posts">
                        <thead>
                        <tr>
                            <th><?php $this->container['languages']->ta('page_posts_table_title'); ?></th>
                            <th><?php $this->container['languages']->ta('page_posts_table_author'); ?></th>
                            <th><?php $this->container['languages']->ta('page_posts_table_date'); ?></th>
                            <th><?php $this->container['languages']->ta('page_posts_table_status'); ?></th>
                            <th><?php $this->container['languages']->ta('page_posts_table_select'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php

                        $post = $this->container['database']->query("SELECT * FROM my_blog ORDER BY postDate DESC");
                        $i = 0;
                        foreach ($post as $postinfo) {
                            $i++;
                            ?>
                            <tr>
                                <td>
                                    <a href="{@siteURL@}/my-admin/posts_edit/<?php echo $postinfo['postId']; ?>"><?php echo $postinfo['postTitle']; ?></a>
                                </td>
                                <td><?php echo $this->container['blog']->getInfo("authorName", $postinfo['postId']); ?></td>
                                <td><?php echo date("d-m-Y h:i:s", strtotime($postinfo['postDate'])); ?></td>
                                <td><?php echo ($postinfo['postStatus'] == "publish") ? $this->container['languages']->ta('page_post_create_label_published', true) : (($postinfo['postStatus'] == "pending") ? $this->container['languages']->ta('page_post_create_label_pending_review', true) : $this->container['languages']->ta('page_post_create_label_draft', true)); ?></td>
                                <td><input type="checkbox" name="check_list[]"
                                           value="<?php echo $postinfo['postId']; ?>"></td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
            </div>
            <!-- /.table-responsive -->
        </div>
        <!-- /.col-lg-12 -->
        <div class="col-lg-6">
            <p><?php $this->container['languages']->ta('page_posts_if_check'); ?></p>
            <select name="ifchecked" class="form-control">
                <?php
                if ($this->container['users']->currentUserHasPermission("delete_private_posts") || $this->container['users']->currentUserHasPermission("delete_private_pages") || $this->container['users']->currentUserHasPermission("delete_others_posts") || $this->container['users']->currentUserHasPermission("delete_posts") || $this->container['users']->currentUserHasPermission("delete_published_posts")) {
                    ?>
                    <option selected="selected" value="delete"><?php $this->container['languages']->ta('page_posts_check_delete'); ?></option>
                    <?php
                }
                if ($this->container['users']->currentUserHasPermission("edit_others_posts") || $this->container['users']->currentUserHasPermission("edit_private_posts") || $this->container['users']->currentUserHasPermission("edit_published_posts") || $this->container['users']->currentUserHasPermission("edit_posts")) {
                    ?>
                    <option selected="selected"
                            value="edit"><?php $this->container['languages']->ta('page_posts_check_edit'); ?></option>
                    <?php
                }
                ?>
            </select>
        </div>
        <div class="col-lg-3">
            <p>&nbsp;</p>
            <button type="submit" name="execute" class="btn btn-danger"><?php $this->container['languages']->ta('page_posts_check_button'); ?></button>
        </div>
        </form>
    </div>
</div>
<!-- /.row -->
</div>
<!-- /#page-wrapper -->

</div>
<!-- /#wrapper -->
<?php $this->getFileAdmin('footer'); ?>
<script>
    $(document).ready(function () {
        var table = $('#tables_posts').dataTable({
            language: {
                "sEmptyTable": "<?php $this->container['languages']->ta('_table_sEmptyTable'); ?>",
                "sInfo": "<?php $this->container['languages']->ta('_table_sInfo'); ?>",
                "sInfoEmpty": "<?php $this->container['languages']->ta('_table_sInfoEmpty'); ?>",
                "sInfoFiltered": "<?php $this->container['languages']->ta('_table_sInfoFiltered'); ?>",
                "sInfoPostFix": "",
                "sInfoThousands": ",",
                "sLengthMenu": "<?php $this->container['languages']->ta('_table_sLengthMenu'); ?>",
                "sLoadingRecords": "<?php $this->container['languages']->ta('_table_sLoadingRecords'); ?>",
                "sProcessing": "<?php $this->container['languages']->ta('_table_sProcessing'); ?>",
                "sSearch": "<?php $this->container['languages']->ta('_table_sSearch'); ?>",
                "sZeroRecords": "<?php $this->container['languages']->ta('_table_sZeroRecords'); ?>",
                "oPaginate": {
                    "sFirst": "<?php $this->container['languages']->ta('_table_sFirst'); ?>",
                    "sPrevious": "<?php $this->container['languages']->ta('_table_sPrevious'); ?>",
                    "sNext": "<?php $this->container['languages']->ta('_table_sNext'); ?>",
                    "sLast": "<?php $this->container['languages']->ta('_table_sLast'); ?>"
                },
                "oAria": {
                    "sSortAscending": "<?php $this->container['languages']->ta('_table_sSortAscending'); ?>",
                    "sSortDescending": "<?php $this->container['languages']->ta('_table_sSortDescending'); ?>"
                }
            },
            "aaSorting": [2, 'desc']
        });
    });
</script>

</body>

</html>

