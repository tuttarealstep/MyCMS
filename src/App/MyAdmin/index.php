<?php
    /**
     * User: tuttarealstep
     * Date: 14/04/16
     * Time: 20.03
     */

    if (staff_logged_in()) {
        header("location: " . HOST . "/my-admin/home");
        exit;
    } else {
        header("location: " . HOST . "/my-admin/login");
        exit;
    }
?>