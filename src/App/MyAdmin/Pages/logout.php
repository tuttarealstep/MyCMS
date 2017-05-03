<?php
/*                     *\
|	MYCMS - TProgram    |
\*                     */

$this->container['users']->hideIfStaffNotLogged();
$this->container['users']->logoutAdmin();