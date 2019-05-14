<?php
/*                     *\
|	MyCMS    |
\*                     */

$this->container['users']->hideIfNotLogged();

$this->container['plugins']->applyEvent("beforeUserLogout");

$this->container['users']->logout();