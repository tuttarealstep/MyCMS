<?php
    /*                     *\
    |	MYCMS - TProgram    |
    \*                     */
    hide_if_staff_not_logged();

    global $my_date, $my_db, $my_users, $my_blog;
    define('PAGE_ID', 'admin_posts');
    define('PAGE_NAME', ea('page_posts_name', '1'));

    get_file_admin('header');
    get_page_admin('topbar');

    $info = "";

    if (isset($_POST['execute'])) {
        $user_rank = $my_users->getInfo($_SESSION['staff']['id'], 'rank');
        if ($user_rank >= 2) {
            if ($_POST['ifchecked'] == 'delete') {
                if (!empty($_POST['check_list'])) {
                    foreach ($_POST['check_list'] as $select) {
                        $my_db->query('DELETE FROM my_blog WHERE postID = :select', array('select' => $select));
                        $info = '<div class="row"><div class="alert alert-success">' . ea('page_posts_delete_successfull', '1') . '</div>';
                    }
                } else {
                    $info = '<div class="row"><div class="alert alert-danger">' . ea('page_posts_delete_empty_checklist', '1') . '</div>';
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
    }
?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <?php if (!empty($info)) {
                echo '<br>' . $info . '<br>';
            } ?>
            <h1 class="h1PagesTitle"><?php ea('page_posts_header'); ?> <a href="{@siteURL@}/my-admin/posts_new"
                                                     class="btn btn-primary pull-right"><?php ea('page_posts_header_create_new'); ?></a>
            </h1>
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
                            <th><?php ea('page_posts_table_title'); ?></th>
                            <th><?php ea('page_posts_table_author'); ?></th>
                            <th><?php ea('page_posts_table_category'); ?></th>
                            <th><?php ea('page_posts_table_date'); ?></th>
                            <th><?php ea('page_posts_table_status'); ?></th>
                            <th><?php ea('page_posts_table_select'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                            global $my_db;
                            $post = $my_db->query("SELECT * FROM my_blog WHERE postPOSTED = '1' ORDER BY postDATE");
                            $i = 0;
                            foreach ($post as $postinfo) {
                                $i++;
                                ?>
                                <tr>
                                    <td>
                                        <a href="{@siteURL@}/my-admin/posts_edit/<?php echo $postinfo['postID']; ?>"><?php echo $postinfo['postTITLE']; ?></a>
                                    </td>
                                    <td><?php echo $this->container['blog']->getInfo("authorName", $postinfo['postAUTHOR']); ?></td>
                                    <td><?php echo $postinfo['postCATEGORY']; ?></td>
                                    <td><?php echo $postinfo['postDATE']; ?></td>
                                    <td><?php echo ($postinfo['postSTATUS'] == "publish") ? ea('page_posts_new_label_published', '1') : (($postinfo['postSTATUS'] == "pending") ? ea('page_posts_new_label_pending_review', '1') : ea('page_posts_new_label_draft', '1')); ?></td>
                                    <td><input type="checkbox" name="check_list[]"
                                               value="<?php echo $postinfo['postID']; ?>"></td>
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
            <p><?php ea('page_posts_if_check'); ?></p>
            <select name="ifchecked" class="form-control">
                <option selected="selected" value="delete"><?php ea('page_posts_check_delete'); ?></option>
                <option selected="selected" value="edit"><?php ea('page_posts_check_edit'); ?></option>
            </select>
        </div>
        <div class="col-lg-3">
            <p>&nbsp;</p>
            <button type="submit" name="execute" class="btn btn-danger"><?php ea('page_posts_check_button'); ?></button>
        </div>
        </form>
    </div>
</div>
<!-- /.row -->
</div>
<!-- /#page-wrapper -->

</div>
<!-- /#wrapper -->
<?php get_file_admin('footer'); ?>
<script>
    $(document).ready(function () {
        var table = $('#tables_posts').dataTable({
            language: {
                "sEmptyTable": "<?php ea('_table_sEmptyTable'); ?>",
                "sInfo": "<?php ea('_table_sInfo'); ?>",
                "sInfoEmpty": "<?php ea('_table_sInfoEmpty'); ?>",
                "sInfoFiltered": "<?php ea('_table_sInfoFiltered'); ?>",
                "sInfoPostFix": "",
                "sInfoThousands": ",",
                "sLengthMenu": "<?php ea('_table_sLengthMenu'); ?>",
                "sLoadingRecords": "<?php ea('_table_sLoadingRecords'); ?>",
                "sProcessing": "<?php ea('_table_sProcessing'); ?>",
                "sSearch": "<?php ea('_table_sSearch'); ?>",
                "sZeroRecords": "<?php ea('_table_sZeroRecords'); ?>",
                "oPaginate": {
                    "sFirst": "<?php ea('_table_sFirst'); ?>",
                    "sPrevious": "<?php ea('_table_sPrevious'); ?>",
                    "sNext": "<?php ea('_table_sNext'); ?>",
                    "sLast": "<?php ea('_table_sLast'); ?>"
                },
                "oAria": {
                    "sSortAscending": "<?php ea('_table_sSortAscending'); ?>",
                    "sSortDescending": "<?php ea('_table_sSortDescending'); ?>"
                }
            },
            "aaSorting": [3, 'desc']
        });
    });
</script>

</body>

</html>

