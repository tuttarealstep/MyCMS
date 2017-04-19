<?php
/*                     *\
|	MYCMS - TProgram    |
\*                     */
hideIfStaffNotLogged();

global $my_date, $my_db, $my_blog;
define('PAGE_ID', 'admin_category');
define('PAGE_NAME', ea('page_category_name', '1'));

getFileAdmin('header');
getPageAdmin('topbar');

$info = "";

if (isset($_POST['execute'])) {
    if ($_POST['ifchecked'] == 'delete') {
        if (!empty($_POST['check_list'])) {
            foreach ($_POST['check_list'] as $select) {
                $my_db->query('DELETE FROM my_blog_category WHERE catID = :select', ['select' => $select]);
                $info = '<div class="row"><div class="alert alert-success">' . ea('page_category_delete_successfull', '1') . '</div>';
            }
        } else {
            $info = '<div class="row"><div class="alert alert-danger">' . ea('page_category_delete_empty_checklist', '1') . '</div>';
        }
    }
}
if (isset($_POST['newcategory'])) {
    if (!empty($_POST['name'])) {
        $finder = $my_blog->categoryFinder($_POST['name']);
        if ($finder == true) {
            $info = '<div class="alert alert-danger">' . ea('page_category_error_category_in_use', '1') . '</div>';
            $name = '';
            $description = mySqlSecure($_POST['description']);
        } else {
            $name = addslashes($_POST['name']);
            $description = addslashes($_POST['description']);
            $my_db->query("INSERT INTO my_blog_category (catNAME,catDESCRIPTION) VALUES (:name, :description)", ['name' => $name, 'description' => $description]);
            $info = '<div class="alert alert-success">' . ea('page_category_addedd_succesful', '1') . '</div>';
            $name = '';
            $description = '';
        }
    } else {
        $info = '<div class="alert alert-danger">' . ea('page_category_delete_empty_name', '1') . '</div>';
        $name = mySqlSecure($_POST['name']);
        $description = mySqlSecure($_POST['description']);

    }
}
?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <?php if (!empty($info)) {
                echo '<br>' . $info . '<br>';
            } ?>
            <h1 class="h1PagesTitle"><?php ea('page_category_header'); ?></h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-8">
            <div class="table-responsive">
                <form action="" method="post">
                    <table class="table table-striped table-bordered table-hover" id="tables_posts">
                        <thead>
                        <tr>
                            <th><?php ea('page_category_table_name'); ?></th>
                            <th><?php ea('page_category_table_description'); ?></th>
                            <th><?php ea('page_category_table_post'); ?></th>
                            <th><?php ea('page_category_table_select'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        global $my_db;
                        $category = $my_db->query("SELECT * FROM my_blog_category");
                        $i = 0;
                        foreach ($category as $categoryinfo) {
                            $i++;
                            ?>
                            <tr>
                                <td><?php echo $categoryinfo['catNAME']; ?></td>
                                <td><?php echo $categoryinfo['catDESCRIPTION']; ?></td>
                                <td><?php echo $my_db->single("SELECT COUNT(*) FROM my_blog WHERE postCATEGORY = '" . $categoryinfo['catNAME'] . "'"); ?></td>
                                <td><input type="checkbox" name="check_list[]"
                                           value="<?php echo $categoryinfo['catID']; ?>"></td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
            </div>
            <!-- /.table-responsive -->
            <div class="col-lg-6">
                <p><?php ea('page_posts_if_check'); ?></p>
                <select name="ifchecked" class="form-control">
                    <option value="delete"><?php ea('page_category_check_delete'); ?></option>
                </select>
            </div>
            <div class="col-lg-3">
                <p>&nbsp;</p>
                <button type="submit" name="execute"
                        class="btn btn-danger"><?php ea('page_category_check_button'); ?></button>
            </div>
            </form>
        </div>
        <!-- /.col-lg-12 -->


        <div class="col-lg-4">
            <div class="b_panel panel">
                <div class="panel-heading">
                    <h1 class="panel-title text-center"><?php ea('page_category_add_new_category'); ?></h1>
                </div>
                <form action="" method="post">
                    <div class="panel-body b_panel-body">
                        <div class="panel-body-padding">
                            <input type="text" name="name" class="form-control b_form-control" maxlength="100"
                                   placeholder="<?php ea('page_category_add_new_category_name'); ?>"
                                   value="<?php echo isset($name) ? $name : "" ?>">
                            <br/>
                            <textarea name="description" class="b_form-control" style=" width:100%;min-width:100%;max-width:100%;height:100px;min-height:100px;max-height:100px;padding: 6px 12px;padding: 6px 12px;
        font-size: 14px;
        line-height: 1.428571429;
        color: #555;
        background-color: #fff;
        background-image: none;

        -webkit-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
        transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
        }"
                                      placeholder="<?php ea('page_category_add_new_category_description'); ?>"><?php echo isset($description) ? $description : "" ?></textarea>
                            <br/>
                        </div>

                    </div>
                    <button type="submit" name="newcategory" class="btn btn-primary btn-block b_btn "
                            style="/*border-bottom-left-radius: 4px; border-bottom-right-radius: 4px;*/"><?php ea('page_category_add_new_category_button'); ?></button>
                </form>
            </div>
        </div>
    </div>
    <!-- /.row -->
</div>
<!-- /#page-wrapper -->

</div>
<!-- /#wrapper -->
<?php getFileAdmin('footer'); ?>
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

