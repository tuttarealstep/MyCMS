<?php
/**
 * MyCMS(TProgram) - Project
 * Date: 23/10/2015 Time: 11:31
 */

$this->container['users']->hideIfStaffNotLogged();

define('PAGE_ID', 'admin_users_info');
define('PAGE_NAME', $this->container['languages']->ea('page_users_info_page_name', '1'));


$this->getFileAdmin('header');
$this->getPageAdmin('topbar');

$info = "";

if (isset($_POST['banuser'])) {
    $user_rank = $this->container['users']->getInfo($_SESSION['staff']['id'], 'rank');
    if ($user_rank >= 2) {
        $ban_user_email = $_POST['ban_user_email'];
        $ban_ip = $_POST['ban_ip'];
        $expire_date = $_POST['expire_date'];
        $converted_date = date('Y-m-d H:i:s', $expire_date);
        if (!empty($ban_user_email)) {
            if ($this->container['users']->controlMail($ban_user_email)) {
                $user_banned_id = $this->container['users']->getUserId($ban_user_email);
                $user_banned_ip = $this->container['users']->getInfo($user_banned_id, "ip");
                $this->container['database']->query("INSERT INTO my_users_banned (user_ip,expire_date) VALUES (:user_ip, :user_expire_date)", ["user_ip" => $user_banned_ip, "user_expire_date" => $converted_date]);
            } else {
                if (!empty($ban_ip)) {
                    $this->container['database']->query("INSERT INTO my_users_banned (user_ip,expire_date) VALUES (:user_ip, :user_expire_date)", ["user_ip" => $ban_ip, "user_expire_date" => $converted_date]);
                }
            }
        }

    } else {
        $info = '<div class="alert alert-danger">' . $this->container['languages']->ea('page_ranks_error_4', '1') . '</div>';
    }
}
?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <?php if (!empty($info)) {
                echo '<br>' . $info . '<br>';
            } ?>
            <h1 class="h1PagesTitle"><?php $this->container['languages']->ea('page_users_info_page_name'); ?></h1>
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
                            <th><?php $this->container['languages']->ea('page_users_info_id'); ?></th>
                            <th><?php $this->container['languages']->ea('page_users_info_name'); ?></th>
                            <th><?php $this->container['languages']->ea('page_users_info_surname'); ?></th>
                            <th><?php $this->container['languages']->ea('page_users_info_mail'); ?></th>
                            <th><?php $this->container['languages']->ea('page_users_info_ip'); ?></th>
                            <th><?php $this->container['languages']->ea('page_users_info_rank'); ?></th>
                            <th><?php $this->container['languages']->ea('page_users_info_last_access'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php

                        $users = $this->container['database']->query("SELECT * from my_users ORDER BY id DESC");
                        $i = 0;
                        foreach ($users as $users_info) {
                            $i++;
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
                                <td><?php echo $users_info['rank']; ?></td>
                                <td><?php echo $users_info['last_access']; ?></td>
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

