<?php
define('MY_CMS_PATH', true);
define("LOADER_LOAD_PAGE", false);
include '../../../../src/Bootstrap.php';

if ($app->container['users']->userLoggedIn() && $app->container['users']->currentUserHasPermission("customize"))
{
    if (isset($_POST['action'])) {
        switch ($_POST['action'])
        {
            case 'changeThemeSettings':
                if(isset($_POST['key']) && isset($_POST['value']))
                    $app->container['theme']->setThemeSetting($_POST['key'], $_POST['value']);
                break;
            case 'resetThemeSettings':
                    if(isset($_POST['themeName']))
                        $app->container['theme']->resetThemeSettings($_POST['themeName']);
                break;
        }
    }
}