<?php
/**
 * User: tuttarealstep
 * Date: 27/11/16
 * Time: 19.00
 */

namespace MyCMS\App\Utils\Admin;

class MyCMSAdmin
{
    private $container;

    private $menuArray = [];
    private $subMenuArray = [];

    private $menuArrayCallback = [];
    private $subMenuArrayCallback = [];

    function __construct($container)
    {
        $this->container = $container;
    }

    function initRoutes()
    {
        $my_admin_folder_name = "{-@my-admin@-}";

        $this->container['router']->map('GET', '/my-admin/', '{-@my-admin@-}/index');
        $this->container['router']->map('GET', '/my-admin/index', $my_admin_folder_name . 'index');
        $this->container['router']->map('GET', '/my-admin/login', $my_admin_folder_name . 'login');
        $this->container['router']->map('POST', '/my-admin/login', $my_admin_folder_name . 'login');

        $this->container['router']->map('GET', '/my-admin/logout', $my_admin_folder_name . 'logout');
        $this->container['router']->map('POST', '/my-admin/logout', $my_admin_folder_name . 'logout');

        $this->container['router']->map('GET', '/my-admin/ranks', $my_admin_folder_name . 'ranks');
        $this->container['router']->map('POST', '/my-admin/ranks', $my_admin_folder_name . 'ranks');
        $this->container['router']->map('POST', '/my-admin/menu', $my_admin_folder_name . 'menu');
        $this->container['router']->map('GET', '/my-admin/delete-menu/[*:id]', $my_admin_folder_name . 'menu_delete');
        $this->container['router']->map('POST', '/my-admin/delete-menu/[*:id]', $my_admin_folder_name . 'menu_delete');
        $this->container['router']->map('POST', '/my-admin/category', $my_admin_folder_name . 'category');
        $this->container['router']->map('POST', '/my-admin/comments', $my_admin_folder_name . 'comments');
        $this->container['router']->map('POST', '/my-admin/posts', $my_admin_folder_name . 'posts');
        $this->container['router']->map('POST', '/my-admin/post_create', $my_admin_folder_name . 'post_create');
        $this->container['router']->map('GET', '/my-admin/users_bans', $my_admin_folder_name . 'users_bans');
        $this->container['router']->map('POST', '/my-admin/users_bans', $my_admin_folder_name . 'users_bans');

        $this->container['router']->map('GET', '/my-admin/users_new', $my_admin_folder_name . 'users_new');
        $this->container['router']->map('POST', '/my-admin/users_new', $my_admin_folder_name . 'users_new');

        $this->container['router']->map('GET', '/my-admin/users_info', $my_admin_folder_name . 'users_info');
        $this->container['router']->map('POST', '/my-admin/users_info', $my_admin_folder_name . 'users_info');

        $this->container['router']->map('GET', '/my-admin/settings_general', $my_admin_folder_name . 'settings_general');
        $this->container['router']->map('POST', '/my-admin/settings_general', $my_admin_folder_name . 'settings_general');

        $this->container['router']->map('GET', '/my-admin/settings_user', $my_admin_folder_name . 'settings_user');
        $this->container['router']->map('POST', '/my-admin/settings_user', $my_admin_folder_name . 'settings_user');

        $this->container['router']->map('GET', '/my-admin/my_page', $my_admin_folder_name . 'my_page');
        $this->container['router']->map('POST', '/my-admin/my_page', $my_admin_folder_name . 'my_page');

        $this->container['router']->map('GET', '/my-admin/my_page_new', $my_admin_folder_name . 'my_page_new');
        $this->container['router']->map('POST', '/my-admin/my_page_new', $my_admin_folder_name . 'my_page_new');

        $this->container['router']->map('GET', '/my-admin/page_edit/[i:id]', $my_admin_folder_name . 'my_page_edit');
        $this->container['router']->map('POST', '/my-admin/page_edit/[i:id]', $my_admin_folder_name . 'my_page_edit');

        $this->container['router']->map('GET', '/my-admin/posts_edit/[i:id]', $my_admin_folder_name . 'post_create');
        $this->container['router']->map('POST', '/my-admin/posts_edit/[i:id]', $my_admin_folder_name . 'post_create');

        $this->container['router']->map('GET', '/my-admin/theme_manager', $my_admin_folder_name . 'theme_manager');
        $this->container['router']->map('POST', '/my-admin/theme_manager', $my_admin_folder_name . 'theme_manager');

        $this->container['router']->map('GET', '/my-admin/theme_manager/remove/[i:remove]', $my_admin_folder_name . 'theme_manager');
        $this->container['router']->map('POST', '/my-admin/theme_manager/remove/[i:remove]', $my_admin_folder_name . 'theme_manager');

        $this->container['router']->map('GET', '/my-admin/theme_manager/info/[i:info]', $my_admin_folder_name . 'theme_manager');
        $this->container['router']->map('POST', '/my-admin/theme_manager/info/[i:info]', $my_admin_folder_name . 'theme_manager');

        $this->container['router']->map('GET', '/my-admin/update', $my_admin_folder_name . 'update');
        $this->container['router']->map('POST', '/my-admin/update', $my_admin_folder_name . 'update');

        $this->container['router']->map('GET', '/my-admin/code_editor', $my_admin_folder_name . 'code_editor');
        $this->container['router']->map('POST', '/my-admin/code_editor', $my_admin_folder_name . 'code_editor');

        $this->container['router']->map('GET', '/my-admin/my_page_import', $my_admin_folder_name . 'my_page_import');
        $this->container['router']->map('POST', '/my-admin/my_page_import', $my_admin_folder_name . 'my_page_import');

        $this->container['router']->map('GET', '/my-admin/my_plugin/[*:pluginName]', $my_admin_folder_name . 'my_plugin');
        $this->container['router']->map('POST', '/my-admin/my_plugin/[*:pluginName]', $my_admin_folder_name . 'my_plugin');

        $this->container['router']->map('GET', '/my-admin/[*:page]', $my_admin_folder_name . 'page');
    }

    function checkNotification()
    {
        /*if ($this->container['users']->isAdmin()) {
            if (!isset($_SESSION['my-admin']['notification']['dashboard'])) {
                if ($this->container['theme']->thereIsNewUpdate()) {
                    $_SESSION['my-admin']['notification']['dashboard'] = $this->container['theme']->thereIsNewUpdate(false);
                } else {
                    unset($_SESSION['my-admin']['notification']['dashboard']);
                }
            }
        }*/
        //todo do check update async with js on admin panel
        //todo complete the notification, session or array in the container?
    }

    /**
     * @param $menuId
     * @param $title
     * @param $link
     * @param array $activePageIdArray
     * @param string $iconCode
     * @param string $customClass
     * @param string $customStyle
     * @param string $customLiClass
     * @param string $customLiStyle
     */
    function addMenu($menuId, $title, $link, $activePageIdArray = [], $iconCode = "", $customClass = "", $customStyle = "", $customLiClass = "", $customLiStyle = "")
    {
        if (!isset($this->menuArray[ $menuId ])) {
            $this->menuArray[ $menuId ] = ['title' => $title, 'link' => $link, 'activePageIdArray' => $activePageIdArray, 'iconCode' => $iconCode, 'customClass' => $customClass, 'customStyle' => $customStyle, 'customLiClass' => $customLiClass, 'customLiStyle' => $customLiStyle];
        }
    }

    /**
     * @param $menuId
     * @param $fatherMenuId
     * @param $title
     * @param $link
     * @param array $activePageIdArray
     * @param string $customClass
     * @param string $customStyle
     */
    function addSubMenu($menuId, $fatherMenuId, $title, $link, $activePageIdArray = [], $customClass = "", $customStyle = "")
    {
        if (isset($this->menuArray[ $fatherMenuId ])) {
            $this->subMenuArray[ $menuId ] = ['fatherMenuId' => $fatherMenuId, 'title' => $title, 'link' => $link, 'activePageIdArray' => $activePageIdArray, 'customClass' => $customClass, 'customStyle' => $customStyle];
        }
    }

    /**
     * @param $menuId
     * @param $callback
     */
    function addFunctionMenu($menuId, $callback)
    {
        if (isset($this->menuArray[ $menuId ])) {
            $this->menuArrayCallback[ $menuId ] = $callback;
        }
    }

    /**
     * @param $menuId
     * @param $callback
     * @param $customUrlArguments
     */
    function addFunctionSubMenu($menuId, $callback, $customUrlArguments = null)
    {
        if (isset($this->subMenuArray[ $menuId ])) {
            $this->subMenuArrayCallback[ $menuId ]["callBack"] = $callback;
            if (!empty($customUrlArguments) && is_array($customUrlArguments)) {
                $this->subMenuArrayCallback[ $menuId ]["customUrlArguments"] = $customUrlArguments;
            }
        }
    }

    /**
     * @param bool $return
     * @return bool|string
     */
    function getAllMenu($return = false)
    {

        if (!$this->container['cache']->get('getAllAdminMenu') || !is_array($this->container['cache']->get('getAllAdminMenu'))) {
            $this->container['cache']->set('getAllAdminMenu', $this->menuArray, 3600000);
        } else {
            array_merge($this->menuArray, $this->container['cache']->get('getAllAdminMenu'));
        }

        $varToPrint = "";

        foreach ($this->menuArray as $key => $value) {
            $varToPrint .= $this->getMenu($key, true);
        }

        if ($return)
            return $varToPrint;

        echo $varToPrint;

        return true;
    }

    /**
     * @param $menuId
     * @param bool $return
     * @return bool|string
     */
    function getMenu($menuId, $return = false)
    {
        $varToPrint = "";

        if (!isset($this->menuArray[ $menuId ])) {
            return $varToPrint;
        }

        $iconCode = $this->menuArray[ $menuId ]["iconCode"];
        $classCode = $this->menuArray[ $menuId ]["customClass"];
        $styleCode = $this->menuArray[ $menuId ]["customStyle"];

        $liClass = $this->menuArray[ $menuId ]["customLiClass"];
        $liStyle = $this->menuArray[ $menuId ]["customLiStyle"];

        if (!empty($this->menuArray[ $menuId ]["activePageIdArray"])) {
            $isActive = false;

            foreach ($this->menuArray[ $menuId ]["activePageIdArray"] as $page_id) {
                if (PAGE_ID == $page_id) {
                    $isActive = true;
                }
            }

            if ($isActive) {
                $classCode .= "active ";
            }
        }

        if (isset($this->menuArrayCallback[ $menuId ])) {

            $this->container['plugins']->addEvent("my_plugin_" . $this->container["functions"]->addSpace($menuId), $this->menuArrayCallback[ $menuId ]);
            $this->menuArray[ $menuId ]["link"] = "/my-admin/my_plugin/" . $this->container["functions"]->addSpace($menuId);
        }


        $varToPrint .= "<li " . ((!empty($liClass)) ? "class=\"" . $liClass . "\"" : "") . ((!empty($liStyle)) ? "style=\"" . $liStyle . "\"" : "") . ">";
        $varToPrint .= "<a href=\"" . $this->menuArray[ $menuId ]["link"] . "\"" . ((!empty($classCode)) ? "class=\"" . $classCode . "\"" : "") . ((!empty($styleCode)) ? "style=\"" . $styleCode . "\"" : "") . 'data-toggle="collapse" data-target="#' . $menuId . '" aria-expanded="true"' . ">" . ((!empty($iconCode)) ? $iconCode . " " : "") . $this->menuArray[ $menuId ]["title"] . "</a>";
        $varToPrint .= "</li>";

        if ($return)
            return $varToPrint;

        echo $varToPrint;

        return true;
    }

    /**
     * @param bool $return
     * @return bool|string
     */
    function getAllSubMenu($return = false)
    {

        if (!$this->container['cache']->get('getAllAdminSubMenu')) {
            $this->container['cache']->set('getAllAdminSubMenu', $this->subMenuArray, 3600000);
        } else {
            array_merge($this->subMenuArray, $this->container['cache']->get('getAllAdminSubMenu'));
        }

        $varToPrint = "";

        foreach ($this->menuArray as $key => $value) {
            $varToPrintTmp = "";
            foreach ($this->subMenuArray as $keySub => $valueSub) {
                if ($key == $this->subMenuArray[ $keySub ]['fatherMenuId']) {
                    $varToPrintTmp .= $this->getSubMenu($keySub, true);
                }
            }

            if ($varToPrintTmp != "") {
                $varToPrint .= '<ul class="nav nav-second-level collapse" id="' . $key . '" aria-expanded="false">';
                $varToPrint .= $varToPrintTmp;
                $varToPrint .= '</ul>';
            }
        }


        if ($return)
            return $varToPrint;

        echo $varToPrint;

        return true;
    }

    /**
     * @param $menuId
     * @param bool $return
     * @return bool|string
     */
    function getSubMenu($menuId, $return = false)
    {
        $varToPrint = "";

        if (!isset($this->subMenuArray[ $menuId ])) {
            return $varToPrint;
        }

        $classCode = $this->subMenuArray[ $menuId ]["customClass"];
        $styleCode = $this->subMenuArray[ $menuId ]["customStyle"];

        if (!empty($this->subMenuArray[ $menuId ]["activePageIdArray"])) {
            $isActive = false;

            foreach ($this->subMenuArray[ $menuId ]["activePageIdArray"] as $page_id) {
                if (PAGE_ID == $page_id) {
                    $isActive = true;
                }
            }

            if ($isActive) {
                $classCode .= "active ";
            }
        }

        $this->checkEvents($menuId);

        $varToPrint .= "<li>";
        $varToPrint .= "<a href=\"" . $this->subMenuArray[ $menuId ]["link"] . "\"" . ((!empty($classCode)) ? "class=\"" . $classCode . "\"" : "") . ((!empty($styleCode)) ? "style=\"" . $styleCode . "\"" : "") . ">" . $this->subMenuArray[ $menuId ]["title"] . "</a>";
        $varToPrint .= "</li>";

        if ($return)
            return $varToPrint;

        echo $varToPrint;

        return true;
    }

    /**
     * @param $menuId
     */
    function checkEvents($menuId)
    {
        if (isset($this->subMenuArrayCallback[ $menuId ])) {
            $this->container['plugins']->addEvent("my_plugin_" . $this->container["functions"]->addSpace($menuId), $this->subMenuArrayCallback[ $menuId ]["callBack"]);
            $this->subMenuArray[ $menuId ]["link"] = "/my-admin/my_plugin/" . $this->container["functions"]->addSpace($menuId);

            if (isset($this->subMenuArrayCallback[ $menuId ]["customUrlArguments"])) {
                $this->subMenuArray[ $menuId ]["link"] .= "?" . http_build_query($this->subMenuArrayCallback[ $menuId ]["customUrlArguments"]);
            }
        }
    }

    function userMenu()
    {
        ?>
        <ul class="user_menu pull-right">
            <li>
                {@user_name@} {@user_surname@}
            </li>
            <li>
                <a href="{@siteURL@}/my-admin/settings_general"><i class="fa fa-gear fa-fw"></i></a>
            </li>
            <li>
                <a href="{@siteURL@}"><i class="fa fa-home fa-fw"></i></a>
            </li>
            <li class="divider"></li>
            <li>
                <a href="{@siteURL@}/my-admin/logout"><i class="fa fa-sign-out fa-fw"></i></a>
            </li>
        </ul>
        <?php
    }

    function newUpdateNotification()
    {
        if ($this->container['theme']->thereIsNewUpdate()) {
            echo '<span class="badge" style="margin-top:16px; background-color: #E53935">!</span> ';
        }
    }

    /**
     * @param $pageNAME
     * @param $callBack
     */
    function addAdminPage($pageNAME, $callBack)
    {
        if (is_callable($callBack)) {
            //die(strtolower($this->container["functions"]->addSpace($pageNAME)));
            $this->container['plugins']->addEvent("my_plugin_" . $this->container["functions"]->addSpace($pageNAME), $callBack);
        }
    }

    function initPlugins()
    {
        $this->container['plugins']->addEvent('adminHead', '');
        $this->container['plugins']->addEvent('adminFooter', '');

        $this->container['plugins']->addEvent('adminUserMenu', [$this, 'userMenu']);
        $this->container['plugins']->addEvent('adminNewUpdateNotification', [$this, 'newUpdateNotification']);


        $this->container['plugins']->addEvent('addAdminMenu', [$this, 'addMenu'], 1, 9);
        $this->container['plugins']->addEvent('addAdminSubMenu', [$this, 'addSubMenu'], 1, 7);

        $this->container['plugins']->addEvent('addAdminFunctionMenu', [$this, 'addFunctionMenu'], 1, 2);
        $this->container['plugins']->addEvent('addAdminFunctionSubMenu', [$this, 'addFunctionSubMenu'], 1, 3);

        $this->addDefaultMenu();

        $this->container['plugins']->addEvent('getAdminMenu', [$this, 'getMenu']);
        $this->container['plugins']->addEvent('getAllAdminMenu', [$this, 'getAllMenu']);

        $this->container['plugins']->addEvent('getAdminSubMenu', [$this, 'getSubMenu']);
        $this->container['plugins']->addEvent('getAllAdminSubMenu', [$this, 'getAllSubMenu']);


        $this->container['plugins']->addEvent('addAdminPage', [$this, 'addAdminPage'], 1, 2);

    }

    function addDefaultMenu()
    {
        $this->container['plugins']->applyEvent('addAdminMenu', "dashboard", $this->container["languages"]->ta("page_home_page_name", true), "{@siteURL@}/my-admin/home", ['admin_home'], '<i class="fa fa-dashboard fa-fw fa-4x icon_menu_topbar" style="color: #F44336;"></i>');

        $this->container['plugins']->applyEvent('addAdminMenu', "menu_posts", $this->container["languages"]->ta("page_posts_name", true), "#", ['admin_posts', 'admin_posts_new'], '<i class="fa fa-thumb-tack fa-fw fa-3x icon_menu_topbar" style="color: #E91E63;"></i>');
        $this->container['plugins']->applyEvent('addAdminSubMenu', "sub_menu_posts", "menu_posts", $this->container["languages"]->ta("page_posts_all", true), "{@siteURL@}/my-admin/posts", ['admin_posts']);
        $this->container['plugins']->applyEvent('addAdminSubMenu', "sub_menu_post_create", "menu_posts", $this->container["languages"]->ta("page_post_create", true), "{@siteURL@}/my-admin/post_create", ['admin_post_create']);

        $this->container['plugins']->applyEvent('addAdminMenu', "comments", $this->container["languages"]->ta("page_comments_page_name", true), "{@siteURL@}/my-admin/comments", ['admin_comments'], '<i class="fa fa-comment fa-fw fa-3x icon_menu_topbar" style="color: #9C27B0;"></i>');

        $this->container['plugins']->applyEvent('addAdminMenu', "admin_category", $this->container["languages"]->ta("page_category_name", true), "{@siteURL@}/my-admin/category", ['admin_category'], '<i class="fa fa-cubes fa-fw fa-3x icon_menu_topbar" style="color: #673AB7;"></i>');

        $this->container['plugins']->applyEvent('addAdminMenu', "admin_menu", $this->container["languages"]->ta("page_menu_page_name", true), "{@siteURL@}/my-admin/menu", ['admin_menu'], '<i class="fa fa-link fa-fw fa-3x icon_menu_topbar" style="color: #3F51B5;"></i>');

        $this->container['plugins']->applyEvent('addAdminMenu', "admin_pages", $this->container["languages"]->ta("page_pages_page_name", true), "{@siteURL@}/my-admin/my_page", ['admin_pages'], '<i class="fa fa-file fa-fw fa-3x icon_menu_topbar" style="color: #2196F3;"></i>');

        $this->container['plugins']->applyEvent('addAdminMenu', "menu_user", $this->container["languages"]->ta("topbar_menu_users", true), "#", ['admin_ranks', 'admin_users_bans', 'admin_users_info'], '<i class="fa fa-user fa-fw fa-3x icon_menu_topbar" style="color: #03A9F4;"></i>');
        $this->container['plugins']->applyEvent('addAdminSubMenu', "admin_ranks", "menu_user", $this->container["languages"]->ta("page_menu_page_ranks", true), "{@siteURL@}/my-admin/ranks", ['admin_ranks']);
        $this->container['plugins']->applyEvent('addAdminSubMenu', "admin_users_bans", "menu_user", $this->container["languages"]->ta("page_users_bans_page_name", true), "{@siteURL@}/my-admin/users_bans", ['admin_users_bans']);
        $this->container['plugins']->applyEvent('addAdminSubMenu', "admin_users_info", "menu_user", $this->container["languages"]->ta("page_users_info_page_name", true), "{@siteURL@}/my-admin/users_info", ['admin_users_info']);
        $this->container['plugins']->applyEvent('addAdminSubMenu', "admin_users_new", "menu_user", $this->container["languages"]->ta("page_users_new_page_name", true), "{@siteURL@}/my-admin/users_new", ['admin_users_new']);

        $this->container['plugins']->applyEvent('addAdminMenu', "theme_manager", $this->container["languages"]->ta("page_theme_manager", true), "{@siteURL@}/my-admin/theme_manager", ['theme_manager'], '<i class="fa fa-picture-o fa-fw fa-3x icon_menu_topbar" style="color: #00BCD4;"></i>');

        $this->container['plugins']->applyEvent('addAdminMenu', "menu_settings", $this->container["languages"]->ta("page_settings_page_name", true), "#", ['admin_settings_general', 'admin_settings_blog', 'admin_xml_command', 'admin_settings_style'], '<i class="fa fa-gear fa-fw fa-3x icon_menu_topbar" style="color: #009688;"></i>');
        $this->container['plugins']->applyEvent('addAdminSubMenu', "admin_settings_general", "menu_settings", $this->container["languages"]->ta("page_settings_general", true), "{@siteURL@}/my-admin/settings_general", ['admin_settings_general']);
        $this->container['plugins']->applyEvent('addAdminSubMenu', "admin_settings_blog", "menu_settings", $this->container["languages"]->ta("page_settings_blog", true), "{@siteURL@}/my-admin/settings_blog", ['admin_settings_blog']);
        $this->container['plugins']->applyEvent('addAdminSubMenu', "admin_settings_style", "menu_settings", $this->container["languages"]->ta("page_settings_style", true), "{@siteURL@}/my-admin/settings_style", ['admin_settings_style']);
        $this->container['plugins']->applyEvent('addAdminSubMenu', "admin_xml_command", "menu_settings", $this->container["languages"]->ta("page_settings_xml_command", true), "{@siteURL@}/my-admin/xml_command", ['admin_xml_command']);
        $this->container['plugins']->applyEvent('addAdminSubMenu', "admin_settings_user", "menu_settings", $this->container["languages"]->ta("page_settings_user", true), "{@siteURL@}/my-admin/settings_user", ['admin_settings_user']);

        $this->container['plugins']->applyEvent('addAdminMenu', "admin_upload", $this->container["languages"]->ta("page_upload", true), "{@siteURL@}/my-admin/upload", ['upload'], '<i class="fa fa-media-o fa-fw fa-3x icon_menu_topbar" style="color: #00BCD4;"></i>');
    }
}