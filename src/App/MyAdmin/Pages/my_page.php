<?php
/*                     *\
|	MYCMS - TProgram    |
\*                     */
hide_if_staff_not_logged();

global $my_date, $my_db, $my_users, $my_blog, $my_theme;
define('PAGE_ID', 'admin_pages');
define('PAGE_NAME', ea('page_pages_page_name', '1'));

get_file_admin('header');
get_page_admin('topbar');

$info = "";

if (isset($_POST['execute'])) {
    $user_rank = $my_users->getInfo($_SESSION['staff']['id'], 'rank');
    if ($user_rank >= 3) {
        if ($_POST['ifchecked'] == 'delete') {
            if (!empty($_POST['check_list'])) {
                foreach ($_POST['check_list'] as $select) {
                    $my_db->query('DELETE FROM my_page WHERE pageID = :select AND pageCANDELETE = "1"', array('select' => $select));
                    $info = '<div class="row"><div class="alert alert-success">' . ea('page_pages_delete_successfully', '1') . '</div>';
                }
            } else {
                $info = '<div class="row"><div class="alert alert-danger">' . ea('page_pages_delete_empty_checklist', '1') . '</div>';
            }
        } elseif ($_POST['ifchecked'] == 'edit') {
            if (!empty($_POST['check_list'])) {
                foreach ($_POST['check_list'] as $select) {
                    header('Location: ' . HOST . '/my-admin/page_edit/' . $select . '');
                    exit();
                }
            }
        } elseif ($_POST['ifchecked'] == 'export') {
            if (!empty($_POST['check_list'])) {
                foreach ($_POST['check_list'] as $select) {
                    $my_theme->my_page_export($select);
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
            <h1 class="h1PagesTitle"><?php ea('page_pages_page_name'); ?> <a style="margin-left: 10px" href="{@siteURL@}/my-admin/my_page_import"
                                                        class="btn btn-primary pull-right"><?php ea('page_pages_header_import'); ?></a>

            <a href="{@siteURL@}/my-admin/my_page_new"
               class="btn btn-primary pull-right"><?php ea('page_pages_header_create_new'); ?></a>
            </h1>
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
                            <th><?php ea('page_pages_table_page-id'); ?></th>
                            <th><?php ea('page_pages_table_page-title'); ?></th>
                            <th><?php ea('page_pages_table_page-url'); ?></th>
                            <th><?php ea('page_pages_table_page_id'); ?></th>
                            <th><?php ea('page_pages_table_status'); ?></th>
                            <th><?php ea('page_pages_table_select'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        global $my_db;
                        $page = $my_db->query("SELECT * FROM my_page WHERE pageCANDELETE = '1'");
                        $i = 0;
                        foreach ($page as $pageinfo) {
                            $i++;
                            ?>
                            <tr>
                                <td><?php echo $pageinfo['pageID']; ?></td>
                                <td>
                                    <a href="{@siteURL@}/my-admin/page_edit/<?php echo $pageinfo['pageID']; ?>"><?php echo remove_space($pageinfo['pageTITLE']); ?></a>
                                </td>
                                <td><?php echo $pageinfo['pageURL']; ?></td>
                                <td><?php echo $pageinfo['pageID_MENU']; ?></td>
                                <td><?php echo ($pageinfo['pagePUBLIC'] == '1') ? ea('page_pages_status_published', '1') : ea('page_pages_status_draft', '1'); ?></td>
                                <td><input type="checkbox" name="check_list[]"
                                           value="<?php echo $pageinfo['pageID']; ?>"></td>
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
                <option selected="" value="delete"><?php ea('page_pages_check_delete'); ?></option>
                <option selected="" value="export"><?php ea('page_pages_check_export'); ?></option>
                <option selected="selected" value="edit"><?php ea('page_pages_check_edit'); ?></option>
            </select>
        </div>
        <div class="col-lg-3">
            <p>&nbsp;</p>
            <button type="submit" name="execute" class="btn btn-danger"><?php ea('page_pages_check_button'); ?></button>
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
        $('#tables_posts').dataTable({
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
            }
        });
    });
</script>

</body>

</html>

