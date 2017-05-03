<?php
/*                     *\
|	MYCMS - TProgram    |
\*                     */

$this->container['users']->hideIfStaffLogged();

define('PAGE_ID', 'admin_login');
define('PAGE_NAME', $this->container['languages']->ta('page_login_page_name', true));

$this->getFileAdmin('header');
?>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-8 col-xs-offset-2">
            <?php
            if (defined("INDEX_ERROR")) {
                ?>
                <div class="login-panel panel"
                     style="margin-bottom: -20%; padding: 8px; border-bottom: 3px solid #b71c1c;">
                    <div class="panel-body login-panel-body">
                        <?php echo INDEX_ERROR; ?>
                    </div>
                </div>
                <?php
            }
            ?>
            <div class="login-panel panel">
                <div class="panel-heading">
                    <h3 class="panel-title text-center"><?php $this->container['languages']->ea('page_login_panel-title') ?></h3>
                </div>
                <div class="panel-body login-panel-body">
                    <form role="form" method="post">
                        <div class="form-group">
                            <input class="form-control b_form-control"
                                   placeholder="<?php $this->container['languages']->ea('page_login_placeholder_email') ?>" name="email" type="email"
                                   value="" autofocus required>
                        </div>
                        <div class="form-group">
                            <input class="form-control b_form-control"
                                   placeholder="<?php $this->container['languages']->ea('page_login_placeholder_password') ?>" name="password"
                                   type="password" value="" required>
                        </div>
                        <div class="checkbox b_checkbox">
                            <label>
                                <input name="remember" type="checkbox"
                                       value="remember_t"><?php $this->container['languages']->ea('page_login_remember') ?>
                            </label>
                        </div>
                        <input type="submit"
                               style="/*border-bottom-left-radius: 4px; border-bottom-right-radius: 4px;*/"
                               class="btn btn-primary btn-block b_btn" name="admin-login"
                               value="<?php $this->container['languages']->ea('page_login_button') ?>"/>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->getFileAdmin('footer'); ?>
</body>
</html>
