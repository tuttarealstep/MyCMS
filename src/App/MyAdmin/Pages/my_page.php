<?php
/*                     *\
|	MYCMS - TProgram    |
\*                     */
$this->container['users']->hideIfNotLogged();

if (!$this->container['users']->currentUserHasPermission("read_private_pages")) {
    throw new MyCMS\App\Utils\Exceptions\MyCMSException("You do not have permission to access this page!", "Permission denied");
}

define('PAGE_ID', 'admin_pages');
define('PAGE_NAME', $this->container['languages']->ta('page_pages_page_name', true));

$this->getFileAdmin('header');
$this->getPageAdmin('topbar');

$info = "";

if (isset($_POST['execute'])) {
        if ($_POST['ifchecked'] == 'delete') {
            if (!empty($_POST['check_list'])) {
                foreach ($_POST['check_list'] as $select) {
                    if ($this->container['users']->currentUserHasPermission("delete_pages"))
                    {
                        $this->container['database']->query('DELETE FROM my_page WHERE pageID = :select AND pageCANDELETE = "1"', ['select' => $select]);
                        $info = '<div class="row"><div class="alert alert-success">' . $this->container['languages']->ea('page_pages_delete_successfully', '1') . '</div>';
                    } else {
                        if($this->container['database']->single('SELECT pagePUBLIC FROM my_page WHERE pageID = :select AND pageCANDELETE = "1"', ['select' => $select]) == "1")
                        {
                            if ($this->container['users']->currentUserHasPermission("delete_published_pages")) {
                                $this->container['database']->query('DELETE FROM my_page WHERE pageID = :select AND pageCANDELETE = "1"', ['select' => $select]);
                            }
                        } else {
                            if ($this->container['users']->currentUserHasPermission("delete_private_pages")) {
                                $this->container['database']->query('DELETE FROM my_page WHERE pageID = :select AND pageCANDELETE = "1"', ['select' => $select]);
                            }
                        }
                    }
                }
            } else {
                $info = '<div class="row"><div class="alert alert-danger">' . $this->container['languages']->ea('page_pages_delete_empty_checklist', '1') . '</div>';
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
                    if ($this->container['users']->currentUserHasPermission("export"))
                    {
                        $this->container['theme']->my_page_export($select);
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
            <h1 class="h1PagesTitle"><?php $this->container['languages']->ta('page_pages_page_name'); ?>
                <?php
                if ($this->container['users']->currentUserHasPermission("import")) {
                    ?>
                    <a style="margin-left: 10px" href="{@siteURL@}/my-admin/my_page_import" class="btn btn-primary pull-right"><?php $this->container['languages']->ea('page_pages_header_import'); ?></a>
                <?php }  if ($this->container['users']->currentUserHasPermission("publish_pages")) {
                ?>
                <a href="{@siteURL@}/my-admin/my_page_new" class="btn btn-primary pull-right"><?php $this->container['languages']->ea('page_pages_header_create_new'); ?></a>
                <?php }
                ?>
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
                            <th><?php $this->container['languages']->ea('page_pages_table_page-id'); ?></th>
                            <th><?php $this->container['languages']->ea('page_pages_table_page-title'); ?></th>
                            <th><?php $this->container['languages']->ea('page_pages_table_page-url'); ?></th>
                            <th><?php $this->container['languages']->ea('page_pages_table_page_id'); ?></th>
                            <th><?php $this->container['languages']->ea('page_pages_table_status'); ?></th>
                            <th><?php $this->container['languages']->ea('page_pages_table_select'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $query = "SELECT * FROM my_page WHERE pageCANDELETE = '1' AND 1 = 1 ";
                        $permissions = false;
                        if($this->container['users']->currentUserHasPermission("edit_private_pages") || $this->container['users']->currentUserHasPermission("delete_private_pages"))
                        {
                            $query .= "AND pagePUBLIC = '0'";

                            if($this->container['users']->currentUserHasPermission("delete_published_pages")
                                || $this->container['users']->currentUserHasPermission("edit_published_pages"))
                            {
                                $query .= "OR pagePUBLIC = '1'";
                            }

                            $permissions = true;
                        } else {
                            if($this->container['users']->currentUserHasPermission("delete_published_pages")
                                || $this->container['users']->currentUserHasPermission("edit_published_pages"))
                            {
                                $query .= "AND pagePUBLIC = '1'";
                                $permissions = true;
                            }
                        }

                        $page = [];
                        if($permissions)
                        {
                            $page = $this->container['database']->query($query);
                        }

                        foreach ($page as $pageinfo) {
                            ?>
                            <tr>
                                <td><?php echo $pageinfo['pageID']; ?></td>
                                <td>
                                    <a href="{@siteURL@}/my-admin/page_edit/<?php echo $pageinfo['pageID']; ?>"><?php echo $this->container['functions']->removeSpace($pageinfo['pageTITLE']); ?></a>
                                </td>
                                <td><?php echo $pageinfo['pageURL']; ?></td>
                                <td><?php echo $pageinfo['pageID_MENU']; ?></td>
                                <td><?php echo ($pageinfo['pagePUBLIC'] == '1') ? $this->container['languages']->ea('page_pages_status_published', '1') : $this->container['languages']->ea('page_pages_status_draft', '1'); ?></td>
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
            <p><?php $this->container['languages']->ea('page_posts_if_check'); ?></p>
            <select name="ifchecked" class="form-control">
                <?php
                if ($this->container['users']->currentUserHasPermission("delete_published_pages") || $this->container['users']->currentUserHasPermission("delete_private_pages") || $this->container['users']->currentUserHasPermission("delete_pages") ) {
                    ?>
                    <option selected=""
                            value="delete"><?php $this->container['languages']->ea('page_pages_check_delete'); ?></option>
                    <?php
                }
                if ($this->container['users']->currentUserHasPermission("export")) {
                ?>
                <option selected=""
                        value="export"><?php $this->container['languages']->ea('page_pages_check_export'); ?></option>
                <?php }
                if ($this->container['users']->currentUserHasPermission("edit_published_pages") || $this->container['users']->currentUserHasPermission("edit_pages") || $this->container['users']->currentUserHasPermission("edit_private_pages") ) {
                ?>
                <option selected="selected"
                        value="edit"><?php $this->container['languages']->ea('page_pages_check_edit'); ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-lg-3">
            <p>&nbsp;</p>
            <button type="submit" name="execute"
                    class="btn btn-danger"><?php $this->container['languages']->ea('page_pages_check_button'); ?></button>
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

