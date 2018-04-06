<?php
/*                     *\
|	MYCMS - TProgram    |
\*                     */

$this->container['users']->hideIfStaffNotLogged();

define('PAGE_ID', 'admin_home');
define('PAGE_NAME', $this->container['languages']->ta('page_home_page_name', true));

$this->getFileAdmin('header');
$this->getPageAdmin('topbar');

?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="h1PagesTitle"><?php $this->container['languages']->ta('page_home_page_header'); ?></h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 col-md-4 col-lg-offset-0 col-md-offset-0">
            <div class="panel b_panel">
                <div class="panel-heading text-center">
                    <i class="fa fa-info fa-fw"></i> <?php $this->container['languages']->ta('page_home_general_info'); ?>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">

                    <ul class="list-group">
                        <li class="list-group-item">
                            <span class="badge"><?php echo $this->container['database']->single("SELECT count(*) FROM my_blog"); ?></span>
                            <i class="fa fa-thumb-tack fa-fw"></i> <a
                                    href="{@siteURL@}/my-admin/posts"><?php $this->container['languages']->ta('page_home_general_info_post'); ?></a>
                        </li>
                        <li class="list-group-item">
                            <span class="badge"><?php echo $this->container['database']->single("SELECT count(*) FROM my_blog_post_comments WHERE enable = '1'"); ?></span>
                            <i class="fa fa-comment fa-fw"></i> <a
                                    href="{@siteURL@}/my-admin/comments"><?php $this->container['languages']->ta('page_home_general_info_comments'); ?></a>
                        </li>
                        <li class="list-group-item">
                            <span class="badge"><?php echo $this->container['database']->single("SELECT count(*) FROM my_blog_category"); ?></span>
                            <i class="fa fa-cubes fa-fw"></i> <a
                                    href="{@siteURL@}/my-admin/category"><?php $this->container['languages']->ta('page_home_general_info_category'); ?></a>
                        </li>
                    </ul>
                    <table class="table table-responsive">
                        <thead><?php $this->container['languages']->ta('page_home_info_in_use'); ?></thead>
                        <tr>
                            <td>MyCMS</td>
                            <td><span class="label label-success pull-right">{@my_cms_version@}</span></td>
                        </tr>
                        <tr>
                            <td><?php $this->container['languages']->ta('page_home_info_theme_in_use'); ?></td>
                            <td>
                                <span class="label label-success pull-right">{@templateNAME@} - {@templateVERSION@}</span>
                            </td>
                        </tr>
                        <tr>
                            <td><?php $this->container['languages']->ta('page_home_info_theme_created_by'); ?></td>
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
                    <i class="fa fa-newspaper-o fa-fw"></i> <?php $this->container['languages']->ta('page_home_general_info_notifications'); ?>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="notificationPanel">
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- <div class="well well-lg b_panel" style="background-color: #ffffff">...</div>
             <div class="well well-lg b_panel" style="background-color: #ffffff">...</div>-->
        </div>
        <!--<div class="col-lg-4 col-md-4">
                   <div class="alert alert-info"><?php $this->container['languages']->ea('page_home_danger_info'); ?></div>
                </div>-->
        <!-- /.col-lg-6 -->
    </div>
    <!-- /.row -->
</div>
<!-- /#page-wrapper -->

</div>
<!-- /#wrapper -->

<?php $this->getFileAdmin('footer'); ?>
<script>
    $(document).ready(function ()
    {
        $.ajax({
            url: "{@siteURL@}/src/App/Content/Ajax/checkNewUpdate.php",
            type: "get",
            error: function () {
                alert("AJAX ERROR");
            }
        }).done(function (data) {
            $(".notificationPanel").text(data)
        })

    })

</script>
</body>

</html>
