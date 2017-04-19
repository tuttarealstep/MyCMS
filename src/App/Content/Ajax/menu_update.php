<?php
/*                     *\
|	MYCMS - TProgram    |
\*                     */
define('MY_CMS_PATH', true);
define("LOADER_LOAD_PAGE", false);
include '../../../../src/Bootstrap.php';

if (isset($_POST['menu'])) {
    global $my_db;
    if (staffLoggedIn()) {
        $i = 0;
        $menu_query = $_POST['menu'];
        foreach ($menu_query as $menu) {
            $my_db->query("UPDATE my_menu SET menu_sort = '" . mySqlSecure($i) . "' WHERE menu_id ='" . mySqlSecure($menu) . "'");
            $i++;
        }
    }
}
?>