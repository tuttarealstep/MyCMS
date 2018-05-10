<?php
/*                     *\
|	MYCMS - TProgram    |
\*                     */
$this->container['users']->hideIfNotLogged();

if(!$this->container['users']->currentUserHasPermission("promote_users"))
{
    throw new MyCMS\App\Utils\Exceptions\MyCMSException("You do not have permission to access this page!", "Permission denied");
}

define('PAGE_ID', 'admin_ranks');
define('PAGE_NAME', $this->container['languages']->ta('page_ranks_page_name', true));

$this->getFileAdmin('header');
$this->getPageAdmin('topbar');

$info = "";


if (isset($_POST['rankutente']))
{
        if (!empty($_POST['email_rank'])) {
            $email_rank = $_POST['email_rank'];
            if ($this->container['users']->controlMail($email_rank)) {
                $rank_id = $_POST['rank_id'];
                $this->container['database']->query("UPDATE my_users SET rank = '" . $rank_id . "' WHERE mail = '" . $email_rank . "' LIMIT 1");
                $info = '<div class="alert alert-success">' . $this->container['languages']->ta('page_ranks_error_1', true) . '</div>';
                $username_rank = '';
                $rank_id = '';
            } else {
                $info = '<div class="alert alert-danger">' . $this->container['languages']->ta('page_ranks_error_2', true) . '</div>';
                $email_rank = $_POST['email_rank'];
                $rank_id = $_POST['rank_id'];
            }
        } else {
            $info = '<div class="alert alert-danger">' . $this->container['languages']->ta('page_ranks_error_3', true) . '</div>';
            $email_rank = $_POST['email_rank'];
            $rank_id = $_POST['rank_id'];
        }
}
?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <?php if (!empty($info)) {
                echo '<br>' . $info . '<br>';
            } ?>
            <h1 class="h1PagesTitle"><?php $this->container['languages']->ea('page_ranks_header'); ?></h1>
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
                            <th><?php $this->container['languages']->ea('page_ranks_table_user'); ?></th>
                            <th><?php $this->container['languages']->ea('page_ranks_table_rank'); ?></th>
                            <th><?php $this->container['languages']->ea('page_ranks_table_mail'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php

                        $ranks = $this->container['database']->query("SELECT * from my_users ORDER BY rank DESC");

                        foreach ($ranks as $ranksinfo)
                        {
                            ?>
                            <tr>
                                <td><?php echo $ranksinfo['name'] . ' ' . $ranksinfo['surname']; ?></td>
                                <td><?php $this->container['languages']->ta($ranksinfo['rank']); ?></td>
                                <td><?php echo $ranksinfo['mail']; ?></td>
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

        <div class="col-lg-4">
            <div class="panel b_panel">
                <div class="panel-heading">
                    <h1 class="panel-title text-center"><?php $this->container['languages']->ta('page_ranks_give_user_title'); ?></h1>
                </div>
                <form action="" method="post">
                    <div class="panel-body b_panel-body">
                        <div class="panel-body-padding">
                            <input type="text" name="email_rank"
                                   placeholder="<?php $this->container['languages']->ta('page_ranks_give_user_email'); ?>"
                                   class="form-control b_form-control" maxlength="100"
                                   value="<?php echo (isset($email_rank)) ? $email_rank : ""; ?>">
                            <br/>
                            <span class="label label-success"><?php $this->container['languages']->ta('page_ranks_table_name_rank'); ?></span>
                            <br/><br/>
                            <select name='rank_id' class='dropdown form-control b_form-control'>
                                <?php
                                foreach ($this->container['roles']->getRoles() as $key => $value)
                                {
                                    ?><option value='<?php echo $key; ?>'><?php $this->container['languages']->ta($key); ?></option><?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <button type="submit" name="rankutente"
                            class="btn btn-primary b_btn b_btn_radius btn-block"><?php $this->container['languages']->ea('page_ranks_button_promote'); ?></button>

            </div>
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