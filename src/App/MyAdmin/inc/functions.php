<?php
/*                     *\
|	MYCMS - TProgram    |
\*                     */

require_once('info.php');

//CONTROLLO VERSIONE CMS

if (version_compare($this->container['my_cms_version'], $template_admin['cms_version'], '<')) {

    $message = $template_admin['name'] . ' template requires at least MyCMS ' . $template_admin['cms_version'] . ', Please upgrade!';
    throw new \MyCMS\App\Utils\Exceptions\MyCMSException($message . 'template_admin_001');
}

$this->container['theme']->addStyleScriptAdmin('css', '{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/featherlight/featherlight.min.css');
$this->container['theme']->addStyleScriptAdmin('css', '{@MY_ADMIN_TEMPLATE_PATH@}/Assets/css/bootstrap.min.css');
$this->container['theme']->addStyleScriptAdmin('css', '{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/font-awesome-4.6.3/css/font-awesome.min.css');

$this->container['theme']->addStyleScriptAdmin('script', '{@MY_ADMIN_TEMPLATE_PATH@}/Assets/js/jquery-3.1.0.min.js');
$this->container['theme']->addStyleScriptAdmin('script', '{@MY_ADMIN_TEMPLATE_PATH@}/Assets/js/bootstrap.min.js');
$this->container['theme']->addStyleScriptAdmin('script', '{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/dataTables/jquery.dataTables.js');
$this->container['theme']->addStyleScriptAdmin('script', '{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/dataTables/dataTables.bootstrap.js');
$this->container['theme']->addStyleScriptAdmin('script', '{@MY_ADMIN_TEMPLATE_PATH@}/Assets/js/MyAdmin.js');
$this->container['theme']->addStyleScriptAdmin('script', '{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/featherlight/featherlight.min.js');
?>