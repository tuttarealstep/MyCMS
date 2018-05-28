<?php
/**
 * MyCMS(TProgram) - Project
 * Date: 23/10/2015 Time: 11:31
 */

$this->container['users']->hideIfNotLogged();

if(!$this->container['users']->currentUserHasPermission("list_users"))
{
    throw new MyCMS\App\Utils\Exceptions\MyCMSException("You do not have permission to access this page!", "Permission denied");
}

define('PAGE_ID', 'admin_users_info');
define('PAGE_NAME', $this->container['languages']->ta('page_users_info_page_name', true));

if(isset($_GET['action']))
{
    switch ($_GET['action'])
    {
        case 'edit_user':
            if(!$this->container['users']->currentUserHasPermission("edit_users") || !isset($_GET['id']))
            {
                throw new MyCMS\App\Utils\Exceptions\MyCMSException("You do not have permission to access this page!", "Permission denied");
            }

            if(isset($_POST['updateInfo']))
            {
                if(isset($_POST['name']) && !empty($_POST['name']) )
                {
                    $this->container['database']->query("UPDATE my_users SET name = :name WHERE id = :user_id", ["name" => $_POST['name'], "user_id" => $_GET['id']]);
                }
                $pageTitle = $this->container['languages']->ta('page_users_info_page_name_update', true);

                if(isset($_POST['surname']) && !empty($_POST['surname']))
                {
                    $this->container['database']->query("UPDATE my_users SET surname = :surname WHERE id = :user_id", ["surname" => $_POST['surname'], "user_id" => $_GET['id']]);
                }

                header("location: " . HOST . "/my-admin/users_info");
                exit();
            }
            break;
        case 'delete_user':
            if(!$this->container['users']->currentUserHasPermission("delete_users") || !isset($_GET['id']))
            {
                throw new MyCMS\App\Utils\Exceptions\MyCMSException("You do not have permission to access this page!", "Permission denied");
            }

            $pageTitle = $this->container['languages']->ta('page_users_info_page_name_delete', true);

            if($_GET['id'] == $_SESSION['user']['id'])
            {
                header("location: " . HOST . "/my-admin/users_info");
                exit();
            }

            if(isset($_POST['deleteUser']))
            {
                switch ($_POST['content_info'])
                {
                    case "delete":
                            $this->container['database']->query("DELETE FROM my_blog WHERE postAuthor = :user_id", ["user_id" => $_GET['id']]);
                            $this->container['database']->query("DELETE FROM my_blog_post_comments WHERE author = :user_id", ["user_id" => $_GET['id']]);
                            $this->container['database']->query("DELETE FROM my_media WHERE author = :user_id", ["user_id" => $_GET['id']]);
                            $this->container['database']->query("DELETE FROM my_security_cookie WHERE cookie_user = :user_id", ["user_id" => $_GET['id']]);
                            $this->container['database']->query("DELETE FROM my_users WHERE id = :user_id", ["user_id" => $_GET['id']]);
                        break;
                    case "transfer":
                            $toUser = $_POST['toTransferUser'];
                            $this->container['database']->query("UPDATE my_blog SET postAuthor = :toUser WHERE postAuthor = :user_id", ["toUser" => $toUser, "user_id" => $_GET['id']]);
                            $this->container['database']->query("UPDATE my_blog_post_comments SET author = :toUser WHERE author = :user_id", ["toUser" => $toUser, "user_id" => $_GET['id']]);
                            $this->container['database']->query("UPDATE my_media SET author = :toUser WHERE author = :user_id", ["toUser" => $toUser, "user_id" => $_GET['id']]);
                            $this->container['database']->query("UPDATE my_security_cookie SET cookie_user = :toUser WHERE cookie_user = :user_id", ["toUser" => $toUser, "user_id" => $_GET['id']]);
                        $this->container['database']->query("DELETE FROM my_users WHERE id = :user_id", ["user_id" => $_GET['id']]);
                        break;
                }

                header("location: " . HOST . "/my-admin/users_info");
                exit();
            }
            break;
    }
} else {
    $pageTitle = $this->container['languages']->ta('page_users_info_page_name', true);
}


$this->getFileAdmin('header');
$this->getPageAdmin('topbar');

?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="h1PagesTitle"><?php echo $pageTitle; ?></h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <?php
        if(!isset($_GET['action'])) {
            ?>
            <div class="col-lg-12">
                <div class="table-responsive">
                    <form action="" method="post">
                        <table class="table table-striped table-bordered table-hover" id="tables_posts">
                            <thead>
                            <tr>
                                <th><?php $this->container['languages']->ta('page_users_info_id'); ?></th>
                                <th><?php $this->container['languages']->ta('page_users_info_name'); ?></th>
                                <th><?php $this->container['languages']->ta('page_users_info_surname'); ?></th>
                                <th><?php $this->container['languages']->ta('page_users_info_mail'); ?></th>
                                <th><?php $this->container['languages']->ta('page_users_info_ip'); ?></th>
                                <th><?php $this->container['languages']->ta('page_users_info_rank'); ?></th>
                                <th><?php $this->container['languages']->ta('page_users_info_last_access'); ?></th>
                                <th><?php $this->container['languages']->ta('page_users_info_edit'); ?></th>
                                <th><?php $this->container['languages']->ta('page_users_info_delete'); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php

                            $users = $this->container['database']->query("SELECT * from my_users ORDER BY id DESC");
                            foreach ($users as $users_info) {
                                ?>
                                <tr>
                                    <td><?php echo $users_info['id']; ?></td>
                                    <td><?php echo $users_info['name']; ?></td>
                                    <td><?php echo $users_info['surname']; ?></td>
                                    <td><?php echo $users_info['mail']; ?></td>
                                    <td><?php echo $users_info['ip']; ?> (<a
                                                href="https://who.is/whois-ip/ip-address/<?php echo $users_info['ip']; ?>"
                                                target="_blank">Who is?</a>)
                                    </td>
                                    <td><?php $this->container['languages']->ta($users_info['rank']); ?></td>
                                    <td><?php echo $users_info['last_access']; ?></td>
                                    <td>
                                        <a href="{@siteURL@}/my-admin/users_info?action=edit_user&id=<?php echo $users_info['id']; ?>"
                                           style="text-decoration: underline"><?php $this->container['languages']->ta('page_users_info_edit'); ?></a>
                                    </td>
                                    <td>
                                        <a href="{@siteURL@}/my-admin/users_info?action=delete_user&id=<?php echo $users_info['id']; ?>"
                                           style="text-decoration: underline"><?php $this->container['languages']->ta('page_users_info_delete'); ?></a>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </form>
                </div>
                <!-- /.table-responsive -->
            </div>
            <!-- /.col-lg-12 -->
            <?php
        } else {
            switch ($_GET['action'])
            {
                case 'edit_user':
                    $userInfo = $this->container['database']->row("SELECT * from my_users WHERE id = :user_id", ["user_id" => (int)$_GET['id']]);
                   ?>
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <form method="post">
                            <div class="form-group">
                                <label for="email"><?php $this->container['languages']->ta("page_users_info_email"); ?></label>
                                <input type="email" disabled class="form-control" id="email" value="<?php echo $userInfo["mail"]; ?>" name="email" placeholder="<?php $this->container['languages']->ta("page_users_info_email"); ?>">
                            </div>
                            <div class="form-group">
                                <label for="name"><?php $this->container['languages']->ta("page_users_info_edit_name"); ?></label>
                                <input type="text" class="form-control" id="name" value="<?php echo $userInfo["name"]; ?>" name="name" placeholder="<?php $this->container['languages']->ta("page_users_info_edit_name"); ?>">
                            </div>
                            <div class="form-group">
                                <label for="surname"><?php $this->container['languages']->ta("page_users_info_edit_surname"); ?></label>
                                <input type="text" class="form-control" id="surname" value="<?php echo $userInfo["surname"]; ?>" name="surname" placeholder="<?php $this->container['languages']->ta("page_users_info_edit_surname"); ?>">
                            </div>
                            <button type="submit" class="btn btn-default" name="updateInfo"><?php $this->container['languages']->ta("page_users_info_update_button"); ?></button>
                        </form>
                    </div>
                    <?php
                    break;
                case 'delete_user':
                    $userInfo = $this->container['database']->row("SELECT * from my_users WHERE id = :user_id", ["user_id" => (int)$_GET['id']]);
                    ?>
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <form method="post">
                            <div class="form-group">
                                <label><?php $this->container['languages']->ta("page_users_info_delete_info"); ?></label>
                                <p>ID: <?php echo $userInfo['id']; ?> | <?php echo $userInfo['mail']; ?> | <?php echo $userInfo['name']; ?> | <?php echo $userInfo['surname']; ?></p>
                            </div>
                            <div class="form-group">
                                <label><?php $this->container['languages']->ta("page_users_info_delete_info_contents"); ?></label>
                            </div>
                            <div class="form-group">
                                <label><?php $this->container['languages']->ta("page_users_info_delete_contents_delete"); ?></label>
                                <input type="radio" id="content_info" name="content_info" value="delete" checked/>
                            </div>
                            <div class="form-group">
                                <label><?php $this->container['languages']->ta("page_users_info_delete_contents_transfer"); ?></label>
                                <input type="radio" id="content_info" name="content_info" value="transfer"/>
                                <label><?php $this->container['languages']->ta("page_users_info_delete_contents_transfer_to"); ?></label>
                                <select name="toTransferUser">
                                    <?php
                                    $queryUsers = $this->container['database']->query("SELECT * from my_users WHERE id NOT LIKE :user_id", ["user_id" => (int)$_GET['id']]);
                                    foreach ($queryUsers as $key => $value)
                                    {
                                        ?>
                                        <option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?> <?php echo $value['surname']; ?> (<?php echo $value['mail']; ?>)</option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-default" name="deleteUser"><?php $this->container['languages']->ta("page_users_info_delete_button"); ?></button>
                        </form>
                    </div>
                    <?php
                    break;
            }
        }
        ?>


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

