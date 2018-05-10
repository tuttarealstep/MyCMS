<?php
/**
 * User: tuttarealstep
 * Date: 29/10/17
 * Time: 11.26
 */
define('MY_CMS_PATH', true);
define("LOADER_LOAD_PAGE", false);
include '../../../../src/Bootstrap.php';

$thereIsNewUpdate = $app->container['theme']->thereIsNewUpdate(false);
if ($thereIsNewUpdate[0] == true)
{
    switch ($thereIsNewUpdate[1])
    {
        case 'all_update':
            $update_text = $app->container['languages']->ta('page_home_general_info_update_all', true);
            break;
        case 'core_update':
            $update_text = $app->container['languages']->ta('page_home_general_info_core_update', true);
            break;
        case 'db_update':
            $update_text = $app->container['languages']->ta('page_home_general_info_db_update', true);
            break;
    }

    $app->container['plugins']->addEvent('admin_notifications', function () use ($app, $update_text)
    {
        echo '<div class="alert alert-danger"><span class="badge" style="background-color: #E53935">!</span> <b>' . $update_text . '</b> <a href="{@siteURL@}/my-admin/update" class="btn btn-info" style="float: right; margin-top: -6px;">' . $app->container['languages']->ta('page_home_general_info_button_update', true) . '</a></div>';
    });
}

$app->container['plugins']->applyEvent('admin_notifications');
    /*
if(is_array($this->container['plugins']->applyEvent('admin_notifications')) && !empty($this->container['plugins']->applyEvent('admin_notifications')))
{
    echo "sadsa";
} else {
    echo $this->container['languages']->ta('page_home_no_notifications', true);
}*/
?>