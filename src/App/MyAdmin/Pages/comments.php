<?php
/*                     *\
|	MYCMS - TProgram    |
\*                     */
$this->container['users']->hideIfStaffNotLogged();

define('PAGE_ID', 'admin_comments');
define('PAGE_NAME', $this->container['languages']->ea('page_comments_page_name', '1'));

$this->getFileAdmin('header');
$this->getPageAdmin('topbar');
$info = "";

if (isset($_POST['execute'])) {
    $user_rank = $this->container['users']->getInfo($_SESSION['staff']['id'], 'rank');
    if ($user_rank >= 2) {
        if ($_POST['ifchecked'] == 'delete') {
            if (!empty($_POST['check_list'])) {
                foreach ($_POST['check_list'] as $select) {
                    $this->container['database']->query('DELETE FROM my_blog_post_comments WHERE id = :select', ['select' => $select]);
                    $info = '<div class="row"><div class="alert alert-success">' . $this->container['languages']->ea('page_comments_delete_successfull', '1') . '</div>';
                }
            } else {
                $info = '<div class="row"><div class="alert alert-danger">' . $this->container['languages']->ea('page_comments_delete_empty_checklist', '1') . '</div>';
            }
        }
        if ($_POST['ifchecked'] == 'approve') {
            if (!empty($_POST['check_list'])) {
                foreach ($_POST['check_list'] as $select) {
                    $this->container['database']->query('UPDATE my_blog_post_comments SET enable = "1" WHERE id = :select', ['select' => $select]);
                    $info = '<div class="row"><div class="alert alert-success">' . $this->container['languages']->ea('page_comments_approve_successfull', '1') . '</div>';
                }
            } else {
                $info = '<div class="row"><div class="alert alert-danger">' . $this->container['languages']->ea('page_comments_delete_empty_checklist', '1') . '</div>';
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
            <h1 class="h1PagesTitle"><?php $this->container['languages']->ea('page_comments_header'); ?></h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive">
                <form action="" method="post">
                    <table class="table table-striped table-bordered table-hover" id="tables_posts">
                        <thead>
                        <tr>
                            <th><?php $this->container['languages']->ea('page_comments_table_approved'); ?></th>
                            <th><?php $this->container['languages']->ea('page_comments_table_author'); ?></th>
                            <th><?php $this->container['languages']->ea('page_comments_table_comment'); ?></th>
                            <th><?php $this->container['languages']->ea('page_comments_table_date'); ?></th>
                            <th><?php $this->container['languages']->ea('page_comments_table_select'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $post = $this->container['database']->query("SELECT * FROM my_blog_post_comments ORDER BY enable = '0' DESC");
                        $i = 0;
                        foreach ($post as $postinfo) {
                            $i++;

                            $name = $this->container['users']->getInfo($postinfo['author'], 'name') . ' ' . $this->container['users']->getInfo($postinfo['author'], 'surname');
                            ?>
                            <tr>
                                <td><b><?php if ($postinfo['enable'] == '1') {
                                            $this->container['languages']->ea('page_comments_table_approved_yes');
                                        } else {
                                            $this->container['languages']->ea('page_comments_table_approved_no');
                                        } ?></b></td>
                                <td><?php echo $name; ?></td>
                                <td><?php echo $postinfo['comments']; ?></td>
                                <td><?php echo $postinfo['date']; ?></td>
                                <td><input type="checkbox" name="check_list[]"
                                           value="<?php echo $postinfo['id']; ?>"></td>
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
            <p><?php $this->container['languages']->ea('page_posts_if_check'); ?></p>
            <select name="ifchecked" class="form-control">
                <option value="delete"><?php $this->container['languages']->ea('page_comments_check_delete'); ?></option>
                <option selected="selected" value="approve"><?php $this->container['languages']->ea('page_comments_check_approve'); ?></option>
            </select>
        </div>
        <div class="col-lg-3">
            <p>&nbsp;</p>
            <button type="submit" name="execute"
                    class="btn btn-danger"><?php $this->container['languages']->ea('page_comments_check_button'); ?></button>
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
        $('#tables_posts').dataTable({
            language: {
                "sEmptyTable": "<?php $this->container['languages']->ea('_table_sEmptyTable'); ?>",
                "sInfo": "<?php $this->container['languages']->ea('_table_sInfo'); ?>",
                "sInfoEmpty": "<?php $this->container['languages']->ea('_table_sInfoEmpty'); ?>",
                "sInfoFiltered": "<?php $this->container['languages']->ea('_table_sInfoFiltered'); ?>",
                "sInfoPostFix": "",
                "sInfoThousands": ",",
                "sLengthMenu": "<?php $this->container['languages']->ea('_table_sLengthMenu'); ?>",
                "sLoadingRecords": "<?php $this->container['languages']->ea('_table_sLoadingRecords'); ?>",
                "sProcessing": "<?php $this->container['languages']->ea('_table_sProcessing'); ?>",
                "sSearch": "<?php $this->container['languages']->ea('_table_sSearch'); ?>",
                "sZeroRecords": "<?php $this->container['languages']->ea('_table_sZeroRecords'); ?>",
                "oPaginate": {
                    "sFirst": "<?php $this->container['languages']->ea('_table_sFirst'); ?>",
                    "sPrevious": "<?php $this->container['languages']->ea('_table_sPrevious'); ?>",
                    "sNext": "<?php $this->container['languages']->ea('_table_sNext'); ?>",
                    "sLast": "<?php $this->container['languages']->ea('_table_sLast'); ?>"
                },
                "oAria": {
                    "sSortAscending": "<?php $this->container['languages']->ea('_table_sSortAscending'); ?>",
                    "sSortDescending": "<?php $this->container['languages']->ea('_table_sSortDescending'); ?>"
                }
            }
        });
    });
</script>

</body>

</html>

