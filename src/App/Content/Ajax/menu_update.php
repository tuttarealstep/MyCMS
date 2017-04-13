<?php
    /*                     *\
    |	MYCMS - TProgram    |
    \*                     */
    define('MY_CMS_PATH', true);
    define("LOADER_LOAD_PAGE", false);
    include '../../../../src/Bootstrap.php';

    if (isset($_POST['menu'])) {
        global $my_db;
        if (staff_logged_in()) {
            $i = 0;
            $menu_query = $_POST['menu'];
            foreach ($menu_query as $menu) {
                $my_db->query("UPDATE my_menu SET menu_sort = '" . my_sql_secure($i) . "' WHERE menu_id ='" . my_sql_secure($menu) . "'");
                $i++;
            }
        }
    }
?>