<?php
/*                     *\
|	MYCMS - TProgram    |
\*                     */
define('MY_CMS_PATH', true);
define("LOADER_LOAD_PAGE", false);
include '../../../../src/Bootstrap.php';

if (isset($_POST['menu'])) {
    if ($app->container['users']->staffLoggedIn()) {
        $i = 0;
        $menu_query = $_POST['menu'];
        foreach ($menu_query as $menu) {
            $app->container['database']->query("UPDATE my_menu SET menu_sort = '" . $app->container['security']->mySqlSecure($i) . "' WHERE menu_id ='" . $app->container['security']->mySqlSecure($menu) . "'");
            $i++;
        }
    }
}
?>