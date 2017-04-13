<?php
/**
 * User: tuttarealstep
 * Date: 11/03/17
 * Time: 11.21
 */

    hide_if_staff_not_logged();

    global $my_db, $my_users, $my_blog;
    define('PAGE_ID', 'admin_users_new');
    define('PAGE_NAME', ea('page_users_new_page_name', '1'));

    get_file_admin('header');
    get_page_admin('topbar');

if (isset($_POST['page_new_user_add_new_button'])) {
    // Dati Inviati dal modulo
    $name = (isset($_POST['name'])) ? trim($_POST['name']) : '';
    $surname = (isset($_POST['surname'])) ? trim($_POST['surname']) : '';
    $password = (isset($_POST['password'])) ? trim($_POST['password']) : '';
    $email = htmlentities($_POST['email']);

    if (!get_magic_quotes_gpc())
    {
        $name = addslashes($name);
        $surname = addslashes($surname);
        $password = addslashes($password);
        $email = addslashes($email);
    }

    $register = $this->container['users']->register($this->container['security']->my_sql_secure($email), $this->container['security']->my_sql_secure($password), $this->container['security']->my_sql_secure($name), $this->container['security']->my_sql_secure($surname));
    if ($register["register"] == 1)
    {
        unset($name);
        unset($surname);
        unset($password);
        unset($email);
        $new_user_created = true;
    } else {
        define("INDEX_ERROR", $this->container['languages']->ta($register["error"], true));
        unset($new_user_created);
    }
}
?>
<?php
if (defined("INDEX_ERROR")) {
    ?>
    <div class="container">
        <div class="panel" style="padding: 8px; border-bottom: 3px solid #b71c1c; margin-top: 2%">
            <div class="panel-body login-panel-body">
                <?php echo INDEX_ERROR; ?>
            </div>
        </div>
    </div>
    <?php
}
if (isset($new_user_created) && $new_user_created == true) {
    ?>
    <div class="container">
        <div class="panel" style="padding: 8px; border-bottom: 3px solid #4caf50; margin-top: 2%">
            <div class="panel-body login-panel-body">
                <?php $this->container['languages']->ta("page_new_user_created"); ?>
            </div>
        </div>
    </div>
    <?php
}
?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="h1PagesTitle"><?php ea('page_users_new_title'); ?></h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <form role="form" method="post" action="">
                <div class="form-group">
                    <label><?php ea('page_new_user_name_label'); ?></label>
                    <input class="form-control b_form-control"
                           placeholder="<?php ea('page_new_user_name_placeholder') ?>" name="name"
                           type="text" value="<?php if(isset($name)) { echo $name; } ?>" required>
                </div>
                <div class="form-group">
                    <label><?php ea('page_new_user_surname_label'); ?></label>
                    <input class="form-control b_form-control"
                           placeholder="<?php ea('page_new_user_surname_placeholder') ?>" name="surname"
                           type="text" value="<?php if(isset($surname)) { echo $surname; } ?>" required>
                </div>
                <div class="form-group">
                    <label><?php ea('page_new_user_email_label'); ?></label>
                    <input class="form-control b_form-control"
                           placeholder="<?php ea('page_new_user_email_placeholder') ?>" name="email"
                           type="email" value="<?php if(isset($email)) { echo $email; } ?>" required>
                </div>
                <div class="form-group">
                    <label><?php ea('page_new_user_password_label'); ?></label>
                    <input class="form-control b_form-control"
                           placeholder="<?php ea('page_new_user_password_placeholder') ?>" name="password"
                           type="password" value="" required>
                </div>
                <input type="submit" class="btn btn-primary btn-block b_btn" name="page_new_user_add_new_button"
                       value="<?php ea('page_new_user_add_new_button') ?>"/>
            </form>
        </div>
    </div>
    <!-- /.row -->
</div>
<!-- /#page-wrapper -->
<?php get_file_admin('footer'); ?>
</body>

</html>

