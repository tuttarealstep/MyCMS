<?php
/*                     *\
|	MYCMS - TProgram    |
\*                     */
define('MY_CMS_PATH', true);
define("LOADER_LOAD_PAGE", false);
include '../../../../src/Bootstrap.php';

if(!$app->container['users']->currentUserHasPermission("manage_links"))
{
    throw new MyCMS\App\Utils\Exceptions\MyCMSException("You do not have permission to access this page!", "Permission denied");
}

if (isset($_POST['menu'])) {
    if ($app->container['users']->userLoggedIn()) {
        $i = 0;
        $menu_query = $_POST['menu'];
        foreach ($menu_query as $menu) {
            $app->container['database']->query("UPDATE my_menu SET menu_sort = '" . $app->container['security']->mySqlSecure($i) . "' WHERE menu_id ='" . $app->container['security']->mySqlSecure($menu) . "'");
            $i++;
        }
    }
}
?>