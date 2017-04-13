<?php
    /**
     * User: tuttarealstep
     * Date: 01/07/16
     * Time: 15.26
     */
    define('MY_CMS_PATH', true);
    define("LOADER_LOAD_PAGE", false);
    include '../../../../src/Bootstrap.php';

    global $my_users, $my_db;
    hide_if_staff_not_logged();
    if (staff_logged_in()) {
        $user_rank = $my_users->getInfo($_SESSION['staff']['id'], 'rank');
        if ($user_rank < 3) {
            return;
        }

        if (isset($_POST['file'])) {
            if (file_exists($_POST['file'])) {
                $file = $_POST['file'];
                $file = file_get_contents($file);
                echo base64_encode($file);
            }
        }
    }