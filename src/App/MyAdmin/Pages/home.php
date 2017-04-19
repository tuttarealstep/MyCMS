<?php
/*                     *\
|	MYCMS - TProgram    |
\*                     */

hideIfStaffNotLogged();

define('PAGE_ID', 'admin_home');
define('PAGE_NAME', ea('page_home_page_name', '1'));

getFileAdmin('header');
getPageAdmin('topbar');

global $my_db, $my_theme;

?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="h1PagesTitle"><?php ea('page_home_page_header'); ?></h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 col-md-4 col-lg-offset-0 col-md-offset-0">
            <div class="panel b_panel">
                <div class="panel-heading text-center">
                    <i class="fa fa-info fa-fw"></i> <?php ea('page_home_general_info'); ?>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">

                    <ul class="list-group">
                        <li class="list-group-item">
                            <span class="badge"><?php echo $my_db->single("SELECT count(*) FROM my_blog WHERE postPOSTED = '1'"); ?></span>
                            <i class="fa fa-thumb-tack fa-fw"></i> <a
                                    href="{@siteURL@}/my-admin/posts"><?php ea('page_home_general_info_post'); ?></a>
                        </li>
                        <li class="list-group-item">
                            <span class="badge"><?php echo $my_db->single("SELECT count(*) FROM my_blog_post_comments WHERE enable = '1'"); ?></span>
                            <i class="fa fa-comment fa-fw"></i> <a
                                    href="{@siteURL@}/my-admin/comments"><?php ea('page_home_general_info_comments'); ?></a>
                        </li>
                        <li class="list-group-item">
                            <span class="badge"><?php echo $my_db->single("SELECT count(*) FROM my_blog_category"); ?></span>
                            <i class="fa fa-cubes fa-fw"></i> <a
                                    href="{@siteURL@}/my-admin/category"><?php ea('page_home_general_info_category'); ?></a>
                        </li>
                    </ul>
                    <table class="table table-responsive">
                        <thead><?php ea('page_home_info_in_use'); ?></thead>
                        <tr>
                            <td>MyCMS</td>
                            <td><span class="label label-success pull-right">{@my_cms_version@}</span></td>
                        </tr>
                        <tr>
                            <td><?php ea('page_home_info_theme_in_use'); ?></td>
                            <td>
                                <span class="label label-success pull-right">{@templateNAME@} - {@templateVERSION@}</span>
                            </td>
                        </tr>
                        <tr>
                            <td><?php ea('page_home_info_theme_created_by'); ?></td>
                            <td><span class="label label-success pull-right">{@templateAUTHOR@}</span></td>
                        </tr>
                        <tr>
                            <td>PHP:</td>
                            <td><span class="label label-success pull-right"><?php echo phpversion(); ?></span></td>
                        </tr>
                    </table>

                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-6 -->
        <div class="col-lg-4 col-md-4">
            <div class="panel b_panel">
                <div class="panel-heading text-center">
                    <i class="fa fa-newspaper-o fa-fw"></i> <?php ea('page_home_general_info_notifications'); ?>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <?php
                    $info = $my_theme->thereIsNewUpdate(false);
                    if ($info[0] == true) {
                        switch ($info[1]) {
                            case 'all_update':
                                $update_text = ea('page_home_general_info_update_all', true);
                                break;
                            case 'core_update':
                                $update_text = ea('page_home_general_info_core_update', true);
                                break;
                            case 'db_update':
                                $update_text = ea('page_home_general_info_db_update', true);
                                break;
                        }
                        echo '<div class="alert alert-danger"><span class="badge" style="background-color: #E53935">!</span> <b>' . $update_text . '</b> <a href="{@siteURL@}/my-admin/update" class="btn btn-info" style="float: right; margin-top: -6px;">' . ea('page_home_general_info_button_update', true) . '</a></div>';
                    } else {
                        echo ea('page_home_no_notifications', true);
                    }
                    ?>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- <div class="well well-lg b_panel" style="background-color: #ffffff">...</div>
             <div class="well well-lg b_panel" style="background-color: #ffffff">...</div>-->
        </div>
        <!--<div class="col-lg-4 col-md-4">
                   <div class="alert alert-info"><?php ea('page_home_danger_info'); ?></div>
                </div>-->
        <!-- /.col-lg-6 -->
    </div>
    <!-- /.row -->
</div>
<!-- /#page-wrapper -->

</div>
<!-- /#wrapper -->

<?php getFileAdmin('footer'); ?>

</body>

</html>
