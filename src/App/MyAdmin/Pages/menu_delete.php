<?php
/*                     *\
|	MYCMS - TProgram    |
\*                     */

$this->container['users']->hideIfNotLogged();

if(!$this->container['users']->currentUserHasPermission("manage_links"))
{
    throw new MyCMS\App\Utils\Exceptions\MyCMSException("You do not have permission to access this page!", "Permission denied");
}

if ($this->container['users']->userLoggedIn()) {

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