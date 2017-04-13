<?php
    hide_if_staff_not_logged();

    $user_rank = $this->container["users"]->getInfo($_SESSION['staff']['id'], 'rank');
    if ($user_rank < 3)
    {
        header('Location: ' . HOST . '/my-admin/home');
        exit();
    }

    global $my_db, $my_users, $my_blog, $my_theme;


    define('PAGE_ID', 'admin_pages_edit');
    define('PAGE_NAME', ea('page_pages_import_title', '1'));

    get_file_admin('header');
    get_page_admin('topbar');


    get_style_script_admin('script');
?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="h1PagesTitle"><?php ea('page_pages_import_title_head'); ?></h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-6">
            <form role="form" method="post" action="">
                <div class="form-group">
                    <div class="form-group">
                        <textarea name="json_code" style="height:210px; width:100%;"></textarea>
                    </div>
                </div>
                <input type="submit" name="import_page_json" class="btn btn-success btn-block"
                       value="<?php ea('page_pages_import_button'); ?>"/>
            </form>
        </div>
    </div>
    <!-- /.row -->
</div>
<!-- /#page-wrapper -->

</div>
<!-- /#wrapper -->

<?php get_file_admin('footer'); ?>

</body>

</html>