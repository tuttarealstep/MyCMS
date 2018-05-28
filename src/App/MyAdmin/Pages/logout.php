<?php
/*                     *\
|	MyCMS    |
\*                     */

$this->container['users']->hideIfNotLogged();
$this->container['users']->logout();