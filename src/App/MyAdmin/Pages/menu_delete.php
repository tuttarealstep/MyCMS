<?php
/*                     *\
|	MYCMS - TProgram    |
\*                     */

$this->container['users']->hideIfStaffNotLogged();


$user_rank = $this->container["users"]->getInfo($_SESSION['staff']['id'], 'rank');
if ($user_rank < 3) {
    header('Location: ' . HOST . '/my-admin/home');
    exit();
}

if ($this->container['users']->staffLoggedIn()) {

    if (isset($_GET['id'])) {

        if (is_numeric($_GET['id'])) {
            $main_sql = $this->container['database']->single("SELECT COUNT(*) FROM my_menu WHERE menu_id = '" . $_GET['id'] . "' AND menu_can_delete = '1' LIMIT 1");
            if ($main_sql > 0) {
                $this->container['database']->query("DELETE FROM my_menu WHERE menu_id = '" . $_GET['id'] . "' ");
                header('Location: ' . HOST . '/my-admin/menu');
                exit();
            }
        } else {
            header('Location: ' . HOST . '/my-admin/menu');
            exit();
        }

    } else {

        header('Location: ' . HOST . '/my-admin/menu');
        exit();

    }
}
?>