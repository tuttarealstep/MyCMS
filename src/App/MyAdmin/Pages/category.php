<?php
$this->container['users']->hideIfNotLogged();

if(!$this->container['users']->currentUserHasPermission("manage_categories"))
{
    throw new MyCMS\App\Utils\Exceptions\MyCMSException("You do not have permission to access this page!", "Permission denied");
}


define('PAGE_ID', 'admin_category');
define('PAGE_NAME', $this->container['languages']->ta('page_category_name', true));

$this->getFileAdmin('header');
$this->getPageAdmin('topbar');

$info = "";

if (isset($_POST['execute'])) {
    if ($_POST['ifchecked'] == 'delete') {
        if (!empty($_POST['check_list'])) {
            foreach ($_POST['check_list'] as $select) {
                if((int)$this->container['database']->single("SELECT COUNT(postId) FROM my_blog_category_relationships WHERE categoryId = :categoryId", ['categoryId' => $select]) > 0)
                {
                    $info = '<div class="alert alert-danger">' . $this->container['languages']->ta('page_category_error_category_in_use', true) . '</div>';
                    break;
                }
                $this->container['database']->query('DELETE FROM my_blog_category WHERE categoryId = :select', ['select' => $select]);
                $info = '<div class="row"><div class="alert alert-success">' . $this->container['languages']->ta('page_category_delete_successfull', true) . '</div>';
            }
        } else {
            $info = '<div class="row"><div class="alert alert-danger">' . $this->container['languages']->ta('page_category_delete_empty_checklist', true) . '</div>';
        }
    } else if ($_POST['ifchecked'] == 'edit')
    {
        $categoryId = isset($_POST['check_list'][0]) ? $_POST['check_list'][0] : 0;
        if((int)$this->container['database']->single("SELECT COUNT(categoryId) FROM my_blog_category WHERE categoryId = :categoryId", ['categoryId' => $categoryId]) > 0)
        {
           $result = $this->container['database']->row("SELECT * FROM my_blog_category WHERE categoryId = :categoryId", ['categoryId' => $categoryId]);
           $name = $result['categoryName'];
           $description = $result['categoryDescription'];
            $categoryId = $result['categoryId'];
        }
    }
}
if (isset($_POST['newcategory'])) {
    if (!empty($_POST['name'])) {
        $finder = $this->container['blog']->categoryFinder($_POST['name']);
        if ($finder == true) {
            $info = '<div class="alert alert-danger">' . $this->container['languages']->ta('page_category_error_already_category_in_use', true) . '</div>';
            $name = '';
            $description = $this->container['security']->mySqlSecure($_POST['description']);
        } else {
            $name = addslashes($_POST['name']);
            $description = addslashes($_POST['description']);
            $this->container['database']->query("INSERT INTO my_blog_category (categoryName,categoryDescription) VALUES (:name, :description)", ['name' => $name, 'description' => $description]);
            $info = '<div class="alert alert-success">' . $this->container['languages']->ta('page_category_added_successful', true) . '</div>';
            $name = '';
            $description = '';
        }
    } else {
        $info = '<div class="alert alert-danger">' . $this->container['languages']->ta('page_category_delete_empty_name', true) . '</div>';
        $name = $this->container['security']->mySqlSecure($_POST['name']);
        $description = $this->container['security']->mySqlSecure($_POST['description']);

    }
}

if(isset($_POST['editcategory']))
{
    if (!empty($_POST['name'])) {

            $valid = true;


            if($this->container['database']->single("SELECT categoryName FROM my_blog_category WHERE categoryId = :categoryId", ['categoryId' => (int)$_POST['categoryId']]) != $_POST['name'])
            {
                $finder = $this->container['blog']->categoryFinder($_POST['name']);
                if ($finder == true) {
                    $info = '<div class="alert alert-danger">' . $this->container['languages']->ta('page_category_error_already_category_in_use', true) . '</div>';
                    $name = '';
                    $description = $this->container['security']->mySqlSecure($_POST['description']);
                    $valid = false;
                }
            }

            if($valid) {
                $categoryId = $_POST['categoryId'];
                $name = addslashes($_POST['name']);
                $description = addslashes($_POST['description']);
                $this->container['database']->query("UPDATE my_blog_category SET categoryName = :name, categoryDescription = :description WHERE categoryId = :categoryId", ['name' => $name, 'description' => $description, 'categoryId' => (int)$categoryId]);
                $info = '<div class="alert alert-success">' . $this->container['languages']->ta('page_category_edit_successful', true) . '</div>';
                $name = '';
                $description = '';
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
            <h1 class="h1PagesTitle"><?php $this->container['languages']->ta('page_category_header'); ?></h1>
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
                            <th><?php $this->container['languages']->ta('page_category_table_name'); ?></th>
                            <th><?php $this->container['languages']->ta('page_category_table_description'); ?></th>
                            <th><?php $this->container['languages']->ta('page_category_table_post'); ?></th>
                            <th><?php $this->container['languages']->ta('page_category_table_select'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $category = $this->container['database']->query("SELECT * FROM my_blog_category");
                        $i = 0;
                        foreach ($category as $categoryinfo) {
                            $i++;
                            ?>
                            <tr>
                                <td><?php echo $categoryinfo['categoryName']; ?></td>
                                <td><?php echo $categoryinfo['categoryDescription']; ?></td>
                                <td><?php echo $this->container['database']->single("SELECT COUNT(*) FROM my_blog_category_relationships WHERE categoryId = :categoryId", ['categoryId' =>  $categoryinfo['categoryId']]); ?></td>
                                <td><input type="checkbox" name="check_list[]"
                                           value="<?php echo $categoryinfo['categoryId']; ?>"></td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
            </div>
            <!-- /.table-responsive -->
            <div class="col-lg-6">
                <p><?php $this->container['languages']->ta('page_posts_if_check'); ?></p>
                <select name="ifchecked" class="form-control">
                    <option value="delete"><?php $this->container['languages']->ta('page_category_check_delete'); ?></option>
                    <option value="edit"><?php $this->container['languages']->ta('page_category_check_edit'); ?></option>
                </select>
            </div>
            <div class="col-lg-3">
                <p>&nbsp;</p>
                <button type="submit" name="execute"
                        class="btn btn-danger"><?php $this->container['languages']->ta('page_category_check_button'); ?></button>
            </div>
            </form>
        </div>
        <!-- /.col-lg-12 -->


        <div class="col-lg-4">
            <div class="b_panel panel">
                <div class="panel-heading">
                    <h1 class="panel-title text-center"><?php $this->container['languages']->ta('page_category_add_new_category'); ?></h1>
                </div>
                <form action="" method="post">
                    <div class="panel-body b_panel-body">
                        <div class="panel-body-padding">
                            <input type="text" name="name" class="form-control b_form-control" maxlength="100"
                                   placeholder="<?php $this->container['languages']->ta('page_category_add_new_category_name'); ?>"
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
                                      placeholder="<?php $this->container['languages']->ta('page_category_add_new_category_description'); ?>"><?php echo isset($description) ? $description : "" ?></textarea>
                            <br/>
                        </div>

                    </div>
                    <?php if(isset($_POST['ifchecked']) && $_POST['ifchecked'] == 'edit') { ?>
                        <input type="hidden" name="categoryId" value="<?php echo $categoryId; ?>">
                        <button type="submit" name="editcategory" class="btn btn-primary btn-block b_btn "><?php $this->container['languages']->ta('page_category_edit_category_button'); ?></button>
                    <?php } else { ?>
                        <button type="submit" name="newcategory" class="btn btn-primary btn-block b_btn "><?php $this->container['languages']->ta('page_category_add_new_category_button'); ?></button>
                    <?php } ?>
                </form>
            </div>
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
            }
        });
    });
</script>

</body>

</html>

