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

    $this->container['theme']->add_style_script_admin('css', '{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/featherlight/featherlight.min.css');
    $this->container['theme']->add_style_script_admin('css', '{@MY_ADMIN_TEMPLATE_PATH@}/Assets/css/bootstrap.min.css');
    $this->container['theme']->add_style_script_admin('css', '{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/font-awesome-4.6.3/css/font-awesome.min.css');

    $this->container['theme']->add_style_script_admin('script', '{@MY_ADMIN_TEMPLATE_PATH@}/Assets/js/jquery-3.1.0.min.js');
    $this->container['theme']->add_style_script_admin('script', '{@MY_ADMIN_TEMPLATE_PATH@}/Assets/js/bootstrap.min.js');
    $this->container['theme']->add_style_script_admin('script', '{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/dataTables/jquery.dataTables.js');
    $this->container['theme']->add_style_script_admin('script', '{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/dataTables/dataTables.bootstrap.js');
    $this->container['theme']->add_style_script_admin('script', '{@MY_ADMIN_TEMPLATE_PATH@}/Assets/js/MyAdmin.js');
    $this->container['theme']->add_style_script_admin('script', '{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/featherlight/featherlight.min.js');
    /*
    //STYLE
    $this->container['theme']->add_style_script_admin('css', '{@MY_ADMIN_TEMPLATE_PATH@}/Assets/css/bootstrap.min.css');
    $this->container['theme']->add_style_script_admin('css', '{@MY_ADMIN_TEMPLATE_PATH@}/Assets/css/plugins/metisMenu/metisMenu.min.css');
    $this->container['theme']->add_style_script_admin('css', '{@MY_ADMIN_TEMPLATE_PATH@}/Assets/css/sb-admin-2.css');
    $this->container['theme']->add_style_script_admin('css', '{@MY_ADMIN_TEMPLATE_PATH@}/Assets/font-awesome-4.1.0/css/font-awesome.min.css');


    $this->container['theme']->add_style_script_admin('script', '{@MY_ADMIN_TEMPLATE_PATH@}/Assets/js/jquery-1.11.0.js');
    $this->container['theme']->add_style_script_admin('script', '{@MY_ADMIN_TEMPLATE_PATH@}/Assets/js/bootstrap.min.js');
    $this->container['theme']->add_style_script_admin('script', '{@MY_ADMIN_TEMPLATE_PATH@}/Assets/js/plugins/metisMenu/metisMenu.min.js');
    $this->container['theme']->add_style_script_admin('script', '{@MY_ADMIN_TEMPLATE_PATH@}/Assets/js/plugins/dataTables/jquery.dataTables.js');
    $this->container['theme']->add_style_script_admin('script', '{@MY_ADMIN_TEMPLATE_PATH@}/Assets/js/plugins/dataTables/dataTables.bootstrap.js');
    $this->container['theme']->add_style_script_admin('script', '{@MY_ADMIN_TEMPLATE_PATH@}/Assets/js/sb-admin-2.js');*/

?>