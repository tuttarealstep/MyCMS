<?php
    /*                     *\
    |	MYCMS - TProgram    |
    \*                     */

    hide_if_staff_not_logged();

    global $my_users;

    $my_users->logout_admin();