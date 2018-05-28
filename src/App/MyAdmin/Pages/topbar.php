<?php
/*                     *\
|	MyCMS    |
\*                     */



$this->container['users']->hideIfNotLogged();

//define('PAGE_ID', 'admin_topbar');

?>
<body>
<div class="container-fluid my_admin_topbar">
    <div class="container">
        <div class="row">
            <button type="button" class="topbar-toggle collapsed" data-toggle="collapse" data-target="#topbar_menu"
                    aria-expanded="false">
                <span class="icon-bar icon-one"></span>
                <span class="icon-bar icon-two"></span>
                <span class="icon-bar icon-three"></span>
            </button>
            <?php $this->container['plugins']->applyEvent('adminUserMenu'); ?>
        </div>
    </div>
</div>

<div class="topbar_menu collapse" id="topbar_menu" aria-expanded="false">
    <div class="navbar-default sidebar" role="navigation">
        <ul class="nav_topbar_menu" id="side-menu">
            <?php $this->container['plugins']->applyEvent('getAllAdminMenu'); ?>
        </ul>

        <?php $this->container['plugins']->applyEvent('getAllAdminSubMenu'); ?>

        <!--<ul class="nav nav-second-level collapse" id="menu_settings" aria-expanded="false">
            <li>
                <a <?php if (PAGE_ID == 'admin_settings_general') {
            echo 'class="active"';
        } ?> href="{@siteURL@}/my-admin/settings_general"><?php $this->container['languages']->ea('page_settings_general'); ?></a>
            </li>
            <li>
                <a <?php if (PAGE_ID == 'admin_settings_blog') {
            echo 'class="active"';
        } ?> href="{@siteURL@}/my-admin/settings_blog"><?php $this->container['languages']->ea('page_settings_blog'); ?></a>
            </li>
            <li>
                <a <?php if (PAGE_ID == 'admin_settings_style') {
            echo 'class="active"';
        } ?> href="{@siteURL@}/my-admin/settings_style"><?php $this->container['languages']->ea('page_settings_style'); ?></a>
            </li>
            <li>
                <a <?php if (PAGE_ID == 'admin_xml_command') {
            echo 'class="active"';
        } ?> href="{@siteURL@}/my-admin/xml_command"><?php $this->container['languages']->ea('page_settings_xml_command'); ?></a>
            </li>
        </ul>
        <!-- /.nav-second-level -->
    </div>
</div>

<?php $this->container['plugins']->applyEvent('admin_notice'); ?>
