<?php
/**
 * User: tuttarealstep
 * Date: 09/04/16
 * Time: 18.47
 */

namespace MyCMS\App\Utils\Theme;

use MyCMS\App\Utils\Exceptions\MyCMSException;
use MyCMS\App\Utils\Management\MyCMSFileManager;
use MyCMS\App\Utils\Management\MyCMSUsers;
use Twig_Environment;
use Twig_Loader_Filesystem;
use ZipArchive;

class MyCMSTheme
{
    public $extension_array = [".html", ".php"];
    public $tag = [];
    public $functions_tag = [];
    public $css = [];
    public $script = [];
    public $meta_tag = [];
    public $indexErrorStyle_array = [];
    public $script_admin_panel = [];
    public $css_admin_panel = [];
    public $small_page = false;
    public $tagCallback = [];

    private $consolePluginCommands = [];

    private $container;

    private $twig_enabled = false;

    function __construct($container)
    {
        $this->container = $container;
        $this->container['users'] = new MyCMSUsers($this->container);
        $this->container['theme'] = $this;

        $this->themeSettings = $this->getThemeSettings();

        if (defined("ENABLE_TWIG_TEMPLATE_ENGINE")) {
            if (ENABLE_TWIG_TEMPLATE_ENGINE) {
                $this->twig_enabled = true;
            }
        }

        $this->addEvents();
    }

    public function getThemeSettings($theme = "")
    {
        if (empty($theme)) {
            $theme = MY_THEME;
        }

        $settings = $this->container["settings"]->getSettingsValue("theme_settings_$theme");
        if ($settings === false) {
            return [];
        }

        return unserialize(base64_decode($settings));
    }

    function addEvents()
    {
        $this->container['plugins']->addEvent("getMenu", [$this, "getMenu"]);
        $this->container['plugins']->addEvent('adminFooter', '');
    }

    public function setContainer($container)
    {
        $this->container = $container;
    }

    function loadThemeFunctions()
    {
        $path_file = $this->getThemePath() . "/inc/functions.php";

        if (!file_exists($path_file)) {
            return false;
        }

        if (isset($_SESSION["staff"]["id"])) {

            if (isset($_SESSION['customizerThemeSession']['theme'])) {
                if (isset($_GET["theme"])) {
                    if ($this->themeExist($this->container["security"]->mySqlSecure($_GET["theme"]))) {
                        $this->userIploadThemeFunctionsByTheme($this->container["security"]->mySqlSecure($_GET["theme"]));
                    } else {
                        $this->userIploadThemeFunctionsByTheme("default");
                    }
                }
            }
        }

        require_once $path_file;

        return true;
    }

    function getThemePath()
    {
        if (file_exists(C_PATH . "/Theme/" . MY_THEME)) {
            $complete_path = $complete_path = C_PATH . "/Theme/" . MY_THEME;
        } else {
            $complete_path = $complete_path = C_PATH . "/Theme/" . "default";
        }

        return $complete_path;
    }

    function themeExist($theme)
    {
        if (file_exists(C_PATH . "/Theme/" . $theme)) {
            return true;
        }

        return false;
    }

    /**
     * MyCMS 6 use Twig for Template Engine but support old template engine.
     * MyCMS 6 with Twig not support MyCMS TAG FUNCTIONS with twig engine variables, use old variables!
     *
     * @return bool
     * @throws MyCMSException
     */
    //todo add loadTheme in plugin system? for support custom engine or other??
    /**
     * New function, support custom theme function
     * @param string $theme
     * @return bool
     */
    function loadThemeFunctionsByTheme($theme = "default")
    {
        $path_file = $this->getCustomThemePath($theme) . "/inc/functions.php";

        if (!file_exists($path_file)) {
            return false;
        }

        require_once $path_file;

        return true;
    }

    function getCustomThemePath($theme)
    {
        if (file_exists(C_PATH . "/Theme/" . $theme)) {
            $complete_path = $complete_path = C_PATH . "/Theme/" . $theme;
        } else {
            $complete_path = $complete_path = C_PATH . "/Theme/" . "default";
        }

        return $complete_path;
    }

    //todo add setPage in plugin system


    function loadAdminFunctions()
    {
        $path_file = MY_ADMIN_PATH . "/inc/functions.php";
        if (!file_exists($path_file)) {
            return false;
        }

        require_once $path_file;

        return true;
    }

    function loadTheme($file, $param)
    {

        $theme_path = $this->getThemePath();

        if (!file_exists($theme_path)) {
            throw new MyCMSException("Theme not found!");
        }

        if ($this->twig_enabled == false && $this->getStyleInfo("require_twig_engine") == true) {
            //TODO send notify (theme need twig enabled)
        }

        if ($this->twig_enabled == true && $this->getStyleInfo("require_twig_engine") == false) {
            $this->twig_enabled = false;
        }

        if ($this->twig_enabled) {

            $file_found = false;
            $file_found_ext = "";

            foreach (array_merge($this->extension_array, [".twig"]) as $file_ext) {
                if (file_exists($theme_path . "/" . $file . $file_ext)) {
                    $file_found = true;
                    $file_found_ext = $file_ext;
                    break;
                } else {
                    $file_found = false;
                }
            }


            $styleInfo = $this->styleInfo(MY_THEME);
            if ($file == $styleInfo["style_error_page"]) {
                header("HTTP/1.0 404 Not Found");
            }


            if ($file_found) {

                $timer_start = microtime(true);

                if (!empty($param)) {
                    foreach ($param as $key => $value) {
                        $_GET[ $key ] = $this->container['security']->mySqlSecure($value);
                    }
                }

                $page_array = [
                    "_GET" => $_GET
                ];

                if (defined('INDEX_ERROR')) {
                    $errors = ['INDEX_ERROR' => $this->indexErrorStyle_array["start_tag"] . INDEX_ERROR . $this->indexErrorStyle_array["finish_tag"]];
                } else {
                    $errors = ['INDEX_ERROR' => ''];
                }


                if (isset($_SESSION['staff']['id'])) {
                    if (isset($_GET['custom_tags'])) {
                        foreach ($this->tag as $tag => $value) {
                            if (isset($_GET[ $tag ])) {
                                $this->tag[ $tag ] = $this->container['security']->mySqlSecure($_GET[ $tag ]);
                            }
                        }
                    }
                }
                $page_array = array_merge($page_array, $this->tag);
                $page_array = array_merge($page_array, $errors);

                $twig_loader = new Twig_Loader_Filesystem($theme_path . "/");

                if (MY_M_DEBUG || ENABLE_TWIG_TEMPLATE_DEBUG) {
                    if ($this->container['settings']->getSettingsValue('site_use_cache') == 'true') {
                        $twig_environment_array = [
                            'cache'       => C_PATH . '/Storage/Cache',
                            'auto_reload' => true,
                            'debug'       => true
                        ];
                    } else {
                        $twig_environment_array = [
                            'auto_reload' => true,
                            'debug'       => true
                        ];
                    }
                } else {
                    if ($this->container['settings']->getSettingsValue('site_use_cache') == 'true') {
                        $twig_environment_array = [
                            'cache' => C_PATH . '/Storage/Cache'
                        ];
                    } else {
                        $twig_environment_array = [];
                    }
                }

                $twig = new Twig_Environment($twig_loader, $twig_environment_array);
                $twig->addExtension(new \Twig_Extension_Debug());
                $twig->addGlobal("container", $this->container);

                if ($file_found_ext == ".php") {
                    ob_start();
                    include $theme_path . "/" . $file . $file_found_ext;
                    $page_loaded = ob_get_contents();
                    ob_end_clean();

                    $page_loaded = $this->parseNoTag($page_loaded);
                    $page_loaded = $this->setTagFunctions($page_loaded);

                    $twig_template = $twig->createTemplate($page_loaded);
                    $page_loaded = $twig_template->render($page_array);
                } else {
                    $page_loaded = $twig->render($file . $file_found_ext, $page_array);

                    $page_loaded = $this->parseNoTag($page_loaded);
                    $page_loaded = $this->setTagFunctions($page_loaded);
                }

                echo $page_loaded;

                $finished = number_format(microtime(true) - $timer_start, 6);
                if (defined("MY_M_DEBUG") && MY_M_DEBUG == true) {
                    echo "\n<!-- MyCMS Page Loader - Page loaded in " . $finished . " sec. -->";
                }
            } else {
                $styleInfo = $this->styleInfo(MY_THEME);
                if ($file == $styleInfo["style_error_page"]) {
                    throw new MyCMSException("Theme: File not found!");
                }

                header('Location: ' . HOST . '/' . $styleInfo["style_error_page"]);

                return false;
            }
        } else {

            $file_found = false;
            $file_found_ext = "";

            foreach ($this->extension_array as $file_ext) {
                if (file_exists($theme_path . "/" . $file . $file_ext)) {
                    $file_found = true;
                    $file_found_ext = $file_ext;
                    break;
                } else {
                    $file_found = false;
                }
            }

            $styleInfo = $this->styleInfo(MY_THEME);
            if ($file == $styleInfo["style_error_page"]) {
                header("HTTP/1.0 404 Not Found");
            }

            if ($file_found) {
                ob_start();
                if (!empty($param)) {
                    foreach ($param as $key => $value) {
                        $_GET[ $key ] = $this->container['security']->mySqlSecure($value);
                    }
                }

                include $theme_path . "/" . $file . $file_found_ext;
                $page_loaded = ob_get_contents();
                ob_end_clean();

                //Remove Space
                if ($this->small_page == true) {
                    $page_loaded = $this->removeSpace($page_loaded);
                }
                $this->setPage($page_loaded);
            } else {
                $styleInfo = $this->styleInfo(MY_THEME);
                if ($file == $styleInfo["style_error_page"]) {
                    throw new MyCMSException("Theme: File not found!");
                }
                header('Location: ' . HOST . '/' . $styleInfo["style_error_page"]);

                return false;
            }
        }

        return false;
    }

    //todo re-make the function because not all functions are supported! // Support for plugin system

    public function getStyleInfo($info)
    {
        $template = null;

        $path_file = $this->getThemePath() . '/inc/info.php';

        @include $path_file;
        if (!empty($info)) {
            return $template[ $info ];
        }

        return false;
    }

    public function styleInfo($style)
    {
        if (!empty($style)) {
            $test = $this->container['database']->iftrue("SELECT style_id FROM my_style WHERE style_path_name = :style_path_name", ["style_path_name" => $this->container['security']->mySqlSecure($style)]);
            if ($test) {
                $styleInfo = $this->container['database']->row("SELECT * FROM my_style WHERE style_path_name = :style_path_name", ["style_path_name" => $this->container['security']->mySqlSecure($style)]);

                return $styleInfo;
            }
        }

        return false;
    }

    public function parseNoTag($page)
    {
        $tmp = explode("{@noTAGS_start@}", $page)[0];
        preg_match("/{@noTAGS_start@}(.*)/s", $page, $match);
        if (count($match) > 1) {
            $tmp = $this->setTag($tmp);

            for ($i = 1; $i <= count($match) - 1; $i++) {
                $tmp .= $match[ $i ];
            }

            preg_match("~(.+?){@noTAGS_end@}(.*)~s", $tmp, $matchEND);
            $c = $matchEND[1];
            $a = $this->parseNoTag($matchEND[2]);

            return $c . $a;
        }

        return $this->setTag($page);
    }

    public function setTag($page)
    {
        if (defined('INDEX_ERROR')) {
            $errors = ['INDEX_ERROR' => $this->indexErrorStyle_array["start_tag"] . INDEX_ERROR . $this->indexErrorStyle_array["finish_tag"]];
        } else {
            $errors = ['INDEX_ERROR' => ''];
        }

        foreach ($errors as $error => $value) {
            $page = str_ireplace('{@' . $error . '@}', $value, $page);
        }

        if (isset($_SESSION['staff']['id'])) {
            if (isset($_GET['custom_tags'])) {
                foreach ($this->tag as $tag => $value) {
                    if (isset($_GET[ $tag ])) {
                        $this->tag[ $tag ] = $this->container['security']->mySqlSecure($_GET[ $tag ]);
                    }
                }
            }
        }


        foreach ($this->tag as $tag => $value) {
            $page = str_ireplace('{@' . $tag . '@}', $value, $page);
            $page = str_ireplace('{@no_' . $tag . '@}', '{@' . $tag . '@}', $page);
        }

        if (!defined("NO_FUNCTION_TAGS")) {
            foreach ($this->tagCallback as $tag => $callBack) {
                preg_match_all("/{@$tag(.*)@}/", $page, $matches);
                if (isset($matches[0])) {
                    foreach ($matches[0] as $keyM => $valueM) {
                        $args = [];
                        $array = explode(" ", $matches[1][ $keyM ]);
                        if (count($array) > 1) {
                            unset($array[0]);
                            foreach ($array as $key => $value) {
                                $tmpExp = explode('=', $value);
                                if (isset($tmpExp[1]) && $tmpExp[1][0] == '"' && $tmpExp[1][ count($tmpExp[1]) - 1 ] == '"') {
                                    $tmpExp[1] = substr($tmpExp[1], 1, -1);
                                    $args[ $tmpExp[0] ] = $tmpExp[1];
                                }
                            }
                            $page = str_replace($matches[0][ $keyM ], $callBack($args, $this->container), $page);
                        } else {
                            $page = str_ireplace($matches[0][ $keyM ], $callBack($args, $this->container), $page);
                        }
                    }
                }
            }
        }


        //NO TAGS
        $page = str_ireplace('{@no_INDEX_ERROR@}', "{@INDEX_ERROR@}", $page);
        $page = str_ireplace('{@no_getSTYLE=css@}', "{@getSTYLE=css@}", $page);

        $page = str_ireplace('{@no_siteURL@}', "{@siteURL@}", $page);
        $page = str_ireplace('{@no_siteNAME@}', "{@siteNAME@}", $page);
        $page = str_ireplace('{@no_siteTEMPLATE@}', "{@siteTEMPLATE@}", $page);
        $page = str_ireplace('{@no_siteLANGUAGE@}', "{@siteLANGUAGE@}", $page);
        $page = str_ireplace('{@no_siteDESCRIPTION@}', "{@siteDESCRIPTION@}", $page);
        $page = str_ireplace('{@no_my_cms_welcome_h1@}', "{@my_cms_welcome_h1@}", $page);


        return $page;
    }

    public function setTagFunctions($page)
    {
        if (empty($this->functions_tag)) {
            return $page;
        }

        $matches_f_f = [];
        $found = [];

        for ($i = 0; $i <= count($this->functions_tag) - 1; $i++) {
            if (preg_match("/" . $this->functions_tag[ $i ]["start"] . "(.*)" . $this->functions_tag[ $i ]["end"] . "/s", $page)) {
                preg_match("/" . $this->functions_tag[ $i ]["start"] . "(.*)" . $this->functions_tag[ $i ]["end"] . "/s", $page, $matches);
                $matches_f_f[] = $matches[1];
                if (!empty($this->functions_tag[ $i ]["param"])) {
                    $user_func = call_user_func($this->functions_tag[ $i ]["function"], $this->functions_tag[ $i ]["param"]);
                } else {
                    $user_func = call_user_func($this->functions_tag[ $i ]["function"]);
                }
                $found = array_merge($found, [["value" => $matches_f_f[ $i ], "function_return" => $user_func]]);
                if ($found[ $i ]["function_return"] == true) {

                } else {
                    $page = str_ireplace($found[ $i ]["value"], "", $page);
                }
            }
        }

        //Remove function tag
        for ($i = 0; $i <= count($this->functions_tag) - 1; $i++) {

            $page = str_ireplace($this->functions_tag[ $i ]["start"], "", $page);
            $page = str_ireplace($this->functions_tag[ $i ]["end"], "", $page);

        }

        return $page;
    }

    //todo make an array with all the "no tags" for make a support to the plugin system / remove or fix unused things

    function removeSpace($page)
    {
        $output = str_replace(["\r\n", "\r"], "\n", $page);
        $lines = explode("\n", $output);
        $new_lines = [];

        foreach ($lines as $i => $line) {
            if (!empty($line)) {
                $new_lines[] = trim($line);
            }

        }

        return implode($new_lines);
    }

    //todo add to plugin system

    function setPage($page)
    {
        $timer_start = microtime(true);
        $page = $this->parseNoTag($page);
        $page = $this->setTagFunctions($page);

        /* if ($admin == false) {
         }*/

        $finished = number_format(microtime(true) - $timer_start, 6);
        if (!defined("NO_PAGE_LOADED_STRING")) {
            if (defined("MY_M_DEBUG") && MY_M_DEBUG == true) {
                $page = $page . "\n<!-- MyCMS Page Loader - Page loaded in " . $finished . " sec. -->";
            }

        }
        echo $page;
    }

    public function addCallBackTag($tag, $callback)
    {
        $array_complete = [$tag => $callback];
        $this->tagCallback = array_merge($this->tagCallback, $array_complete);
    }

    public function indexErrorStyle($start_tag, $finish_tag)
    {
        $array = ["start_tag" => $start_tag, "finish_tag" => $finish_tag];
        $this->indexErrorStyle_array = array_merge($this->indexErrorStyle_array, $array);
    }

    public function noTags($page)
    {
        $page = str_ireplace('{@siteURL@}', "{@no_siteURL@}", $page);
        $page = str_ireplace('{@siteNAME@}', "{@no_siteNAME@}", $page);
        $page = str_ireplace('{@siteTEMPLATE@}', "{@no_siteTEMPLATE@}", $page);
        $page = str_ireplace('{@siteLANGUAGE@}', "{@no_siteLANGUAGE@}", $page);
        $page = str_ireplace('{@siteDESCRIPTION@}', "{@no_siteDESCRIPTION@}", $page);
        $page = str_ireplace('{@my_cms_welcome_h1@}', "{@no_my_cms_welcome_h1@}", $page);

        return $page;
    }

    public function addTag($tag, $value)
    {
        $array_complete = [$tag => $value];
        $this->tag = array_merge($this->tag, $array_complete);
    }

    public function addFunctionsTag($start, $end, $function, $param = null)
    {
        $array_complete = [["start" => $start, "end" => $end, "function" => $function, "param" => $param]];
        $this->functions_tag = array_merge($this->functions_tag, $array_complete);
    }

    public function addMetaTag($page_name, $tag)
    {
        $this->meta_tag[] = [$page_name => $tag];
    }

    public function getMetaTag($page_name)
    {
        foreach ($this->meta_tag as $key => $value) {
            foreach ($value as $value_key => $value_value) {
                if ($value_key == $page_name) {
                    echo $value_value;
                }
            }
        }

        if ($this->container['settings']->getSettingsValue('site_private') == 'true') {
            $this->noRobots();
        }
    }

    function noRobots()
    {

        echo "<meta name='robots' content='noindex,follow' />\n";

    }

    public function addStyleScript($type, $link)
    {
        switch ($type) {
            case 'css':

                $css = '<link href="' . $link . '" rel="stylesheet">' . "\r\n";
                $this->css[] = $css;

                break;
            case 'script':

                $script = '<script src="' . $link . '"></script>' . "\r\n";
                $this->script[] = $script;

                break;
        }
    }

    public function getStyleScript($type, $return = false)
    {
        switch ($type) {
            case 'css':
                $final_css = "";
                foreach ($this->css as $css) {
                    $final_css = $final_css . $css;
                }
                if ($return == true) {
                    return $final_css;
                } else {
                    echo $final_css;
                }
                break;
            case 'script':
                $final_script = "";
                foreach ($this->script as $script) {
                    $final_script = $final_script . $script;
                }
                if ($return == true) {
                    return $final_script;
                } else {
                    echo $final_script;
                }
                break;
        }

        return "";
    }

    public function addStyleScriptAdmin($type, $link)
    {
        switch ($type) {
            case 'css':

                $css = '<link href="' . $link . '" rel="stylesheet">' . "\n";
                $this->css_admin_panel[] = $css;

                break;
            case 'script':

                $script = '<script src="' . $link . '"></script>' . "\n";
                $this->script_admin_panel[] = $script;

                break;
        }
    }

    public function getStyleScriptAdmin($type, $return = false)
    {
        switch ($type) {
            case 'css':
                $final_css = "";
                foreach ($this->css_admin_panel as $css) {
                    $final_css = $final_css . $css;
                }
                if ($return == true) {
                    return $final_css;
                } else {
                    echo $final_css;
                }
                break;
            case 'script':
                $final_script = "";
                foreach ($this->script_admin_panel as $script) {
                    $final_script = $final_script . $script;
                }
                if ($return == true) {
                    return $final_script;
                } else {
                    echo $final_script;
                }
                break;
        }

        return "";
    }

    /**
     * Return the file path in the current theme folder giving file name if it exist.
     * For file in sub theme folder give in the file name the sub dir path.
     *
     * @param $file_name
     * @return bool|string
     * @throws MyCMSException
     */
    public function getFilePathIfExist($file_name)
    {
        $theme_path = $this->getThemePath();
        if (!file_exists($theme_path)) {
            throw new MyCMSException("Theme not found!");
        }

        if (file_exists($theme_path . "/" . $file_name)) {
            return $theme_path . "/" . $file_name;
        } else {
            return false;
        }

    }

    public function getFile($page, $name = null, $page_loader = false, $returnExtension = false)
    {
        $theme_path = $this->getThemePath();
        if (!file_exists($theme_path)) {
            throw new MyCMSException("Theme not found!");
        }

        if (empty($page)) {
            return false;
        }

        switch ($page) {
            case 'header':
                $file_found = false;
                $file_found_ext = "";
                foreach (array_merge($this->extension_array, [".twig"]) as $file_ext) {
                    if (empty($name)) {
                        if (file_exists($theme_path . "/header" . $file_ext)) {
                            $file_found = true;
                            $file_found_ext = $file_ext;
                            break;
                        } else {
                            $file_found = false;
                        }
                    } else {
                        if (file_exists($theme_path . "/header-" . $name . $file_ext)) {
                            $file_found = true;
                            $file_found_ext = $file_ext;
                            break;
                        } else {
                            $file_found = false;
                        }
                    }
                }

                if ($file_found) {
                    if (!empty($name)) {
                        $load_file = 'header-' . $name . $file_found_ext;
                    } else {
                        $load_file = 'header' . $file_found_ext;
                    }

                    if ($returnExtension) {
                        return pathinfo($theme_path . '/' . $load_file, PATHINFO_EXTENSION);
                    } else {
                        if ($page_loader == true) {
                            ob_start();
                            include $theme_path . '/' . $load_file;
                            $set = ob_get_contents();
                            ob_end_clean();

                            return $set;
                        } else {
                            require_once $theme_path . '/' . $load_file;
                        }
                    }
                }
                break;
            case 'footer':

                $file_found = false;
                $file_found_ext = "";
                foreach (array_merge($this->extension_array, [".twig"]) as $file_ext) {
                    if (empty($name)) {
                        if (file_exists($theme_path . "/footer" . $file_ext)) {
                            $file_found = true;
                            $file_found_ext = $file_ext;
                            break;
                        } else {
                            $file_found = false;
                        }
                    } else {
                        if (file_exists($theme_path . "/footer-" . $name . $file_ext)) {
                            $file_found = true;
                            $file_found_ext = $file_ext;
                            break;
                        } else {
                            $file_found = false;
                        }
                    }
                }

                if ($file_found) {
                    if (!empty($name)) {
                        $load_file = 'footer-' . $name . $file_found_ext;
                    } else {
                        $load_file = 'footer' . $file_found_ext;
                    }


                    if ($returnExtension) {
                        return pathinfo($theme_path . '/' . $load_file, PATHINFO_EXTENSION);
                    } else {
                        if ($page_loader == true) {
                            ob_start();
                            include $theme_path . '/' . $load_file;
                            $set = ob_get_contents();
                            ob_end_clean();

                            return $set;
                        } else {
                            /*echo "\n\n<!-- START Plugin -->\n\n";
                                                    $this->container['plugins']->include_plugins("footer");
                            */
                            require_once $theme_path . '/' . $load_file;
                        }
                    }
                }
                break;
            case 'page_loader_top':
                $file_found = false;
                $file_found_ext = "";
                foreach (array_merge($this->extension_array, [".twig"]) as $file_ext) {
                    if (file_exists($theme_path . "/page_loader_top" . $file_ext)) {
                        $file_found = true;
                        $file_found_ext = $file_ext;
                        break;
                    } else {
                        $file_found = false;
                    }
                }
                if ($file_found) {
                    $load_file = 'page_loader_top' . $file_found_ext;


                    if ($returnExtension) {
                        return pathinfo($theme_path . '/' . $load_file, PATHINFO_EXTENSION);
                    } else {
                        if ($page_loader == true) {
                            ob_start();
                            include $theme_path . '/' . $load_file;
                            $set = ob_get_contents();
                            ob_end_clean();

                            return $set;
                        } else {
                            require_once $theme_path . '/' . $load_file;
                        }
                    }
                }
                break;
            case 'page_loader_bottom':

                $file_found = false;
                $file_found_ext = "";

                foreach (array_merge($this->extension_array, [".twig"]) as $file_ext) {
                    if (file_exists($theme_path . "/page_loader_top" . $file_ext)) {
                        $file_found = true;
                        $file_found_ext = $file_ext;
                        break;
                    } else {
                        $file_found = false;
                    }
                }

                if ($file_found) {
                    $load_file = 'page_loader_bottom' . $file_found_ext;


                    if ($returnExtension) {
                        return pathinfo($theme_path . '/' . $load_file, PATHINFO_EXTENSION);
                    } else {
                        if ($page_loader == true) {
                            ob_start();
                            include $theme_path . '/' . $load_file;
                            $set = ob_get_contents();
                            ob_end_clean();

                            return $set;
                        } else {
                            require_once $theme_path . '/' . $load_file;
                        }
                    }
                }
                break;
            case 'page_loader':
                $file_found = false;
                $file_found_ext = "";
                foreach (array_merge($this->extension_array, [".twig"]) as $file_ext) {
                    if (file_exists($theme_path . "/page_loader" . $file_ext)) {
                        $file_found = true;
                        $file_found_ext = $file_ext;
                        break;
                    } else {
                        $file_found = false;
                    }
                }
                if ($file_found) {
                    $load_file = 'page_loader' . $file_found_ext;


                    if ($returnExtension) {
                        return pathinfo($theme_path . '/' . $load_file, PATHINFO_EXTENSION);
                    } else {
                        if ($page_loader == true) {
                            ob_start();
                            include $theme_path . '/' . $load_file;
                            $set = ob_get_contents();
                            ob_end_clean();

                            return $set;
                        } else {
                            require_once $theme_path . '/' . $load_file;
                        }
                    }
                }
                break;
        }

        return false;
    }

    public function getPage($page, $name)
    {
        $theme_path = $this->getThemePath();
        if (!file_exists($theme_path)) {
            throw new MyCMSException("Theme not found!");
        }

        if (empty($page)) {
            return false;
        }

        $load_file = [];
        if (!empty($name)) {
            $load_file[] = $page . '-' . $name . '.php';
        }

        $load_file[] = $page . '.php';
        foreach ($load_file as $page_load) {
            require_once $theme_path . '/' . $page_load;
        }
    }

    public function getFileAdmin($page, $name = null, $page_loader = true)
    {

        $theme_path = MY_ADMIN_PATH . '/Template';

        if (!file_exists($theme_path)) {
            throw new MyCMSException("Theme not found!");
        }

        if (empty($page)) {
            return false;
        }

        switch ($page) {
            case 'header':
                if (!empty($name)) {
                    $load_file = 'header-' . $name . '.php';
                } else {
                    $load_file = 'header.php';
                }

                if ($page_loader == true) {
                    ob_start();
                    include $theme_path . '/' . $load_file;
                    $set = ob_get_contents();
                    ob_end_clean();

                    return $set;
                } else {
                    require_once $theme_path . '/' . $load_file;
                }
                break;
            case 'topbar':
                if (!empty($name)) {
                    $load_file = 'topbar-' . $name . '.php';
                } else {
                    $load_file = 'topbar.php';
                }

                if ($page_loader == true) {
                    ob_start();
                    include $theme_path . '/' . $load_file;
                    $set = ob_get_contents();
                    ob_end_clean();

                    return $set;
                } else {
                    require_once $theme_path . '/' . $load_file;
                }
                break;
            case 'footer':
                if (!empty($name)) {
                    $load_file = 'footer-' . $name . '.php';
                } else {
                    $load_file = 'footer.php';
                }
                if ($page_loader == true) {
                    ob_start();
                    include $theme_path . '/' . $load_file;
                    $set = ob_get_contents();
                    ob_end_clean();

                    return $set;
                } else {
                    require_once $theme_path . '/' . $load_file;
                }
                break;
        }

        return false;
    }

    public function getPageAdmin($page, $name)
    {

        $theme_path = MY_ADMIN_PATH . '/Pages';
        if (!file_exists($theme_path)) {
            throw new MyCMSException("Theme not found!");
        }

        if (empty($page)) {
            return;
        }

        $load_file = [];
        if (!empty($name)) {
            $load_file[] = $page . '-' . $name . '.php';
        }

        $load_file[] = $page . '.php';
        foreach ($load_file as $page_load) {
            require_once $theme_path . '/' . $page_load;
        }
    }

    public function controlMaintenance($url)
    {

        $maintenance = $this->isMaintenance();

        $info = $this->styleInfo(MY_THEME);

        if ($url != $info['style_maintenance_page']) {
            if ($maintenance == true) {
                header('Location: ' . HOST . '/' . $info['style_maintenance_page']);
                exit;
            }
        }

    }

    public function isMaintenance($admin_check = true)
    {
        $maintenance = $this->container['settings']->getSettingsValue('site_maintenance');

        if ($admin_check == true && $this->container['users']->staffLoggedIn()) {
            $maintenance = false;
        } else {
            if ($maintenance == 'true') {
                $maintenance = true;
            } else {
                $maintenance = false;
            }
        }

        return $maintenance;
    }

    public function getAdminUrl($url)
    {
        $url_found = [];

        if ($this->isAdminUrl($url['target'])) {
            preg_match("/{-@my-admin@-}(.*)/i", $url['target'], $url_found);
        }

        if ($url_found[1] == "page") {
            return $url['params']['page'];
        } else {
            return $url_found[1];
        }

    }

    public function isAdminUrl($url)
    {
        if (preg_match("/{-@my-admin@-}/i", $url)) {
            return true;
        }

        return false;
    }

    function adminLoadTheme($file, $param)
    {
        preg_match("/{-@my-admin@-}(.*)/", $file, $file_info);

        $file_name = $file_info[1];

        if ($file_name == "page") {
            $file_name = $param['page'];
        }

        $my_admin_path = MY_ADMIN_PATH . '/Pages';

        if (!file_exists($my_admin_path)) {
            throw new MyCMSException("Admin panel not found!");
        }

        if (file_exists($my_admin_path . '/' . $file_name . '.php')) {
            ob_start();
            if (!empty($param)) {
                foreach ($param as $key => $value) {
                    $_GET[ $key ] = $this->container['security']->mySqlSecure($value);
                }
            }

            include $my_admin_path . "/" . $file_name . '.php';
            $page_loaded = ob_get_contents();
            ob_end_clean();
            $this->setPage($page_loaded);
        } else {
            header('Location: ' . HOST . '/404');

            return false;
        }

        return false;
    }

    function themeUpdate($version, $url)
    {
        $download_url = $url;
        $get_info = @json_decode(file_get_contents($download_url), true);

        $theme_version = @$get_info["theme_version"];
        $theme_my_cms_version = @$get_info["my_cms_version"];

        if (version_compare($version, $theme_version, '<')) {
            if (version_compare($theme_my_cms_version, $this->container['my_cms_version'], '<=')) {
                return [true, $theme_version, true, $theme_my_cms_version];
            } else {
                return [true, $theme_version, false, $theme_my_cms_version];
            }
        } else {
            return [false, $theme_version, false, $theme_my_cms_version];
        }

    }

    function removeDir($dir)
    {
        if (is_dir($dir)) {
            $files = scandir($dir);
            foreach ($files as $file) {
                if ($file != "." && $file != "..") {
                    $this->container['functions']->removeDir("$dir/$file");
                }
            }

            rmdir($dir);
        } else if (file_exists($dir)) {
            unlink($dir);
        }

    }

    function installTheme($fileInfo)
    {
        ignore_user_abort(true);
        set_time_limit(0);

        $rndName = $this->container['security']->myGenerateRandom(5);

        $dir = "." . MY_BASE_PATH . "/tmp/" . $rndName;

        $zipTypes = ['application/zip', 'application/x-zip-compressed',
            'multipart/x-zip', 'application/x-compressed'];

        if (pathinfo($fileInfo['name'], PATHINFO_EXTENSION) != "zip" || !in_array($fileInfo["type"], $zipTypes)) {
            define('INDEX_ERROR', 'Error Upload - <b>You don\'t have uploaded a \'.zip\' file</b>');

            return false;
        }

        if (!mkdir($dir, 0755, true)) {
            return '<div class="alert alert-danger"> Error Upload - <b>Can\'t create tmp folder, check permission!</b> </div>';
        }

        $file = new MyCMSFileManager(["file" => $fileInfo['tmp_name'], "destination" => $dir]);
        $file->move(true);
        $file->extract($dir . "/" . basename($fileInfo['tmp_name']));

        if (!file_exists($dir . "/info.json")) {
            define('INDEX_ERROR', 'Error Install - Can\'t find info.json ' . $dir . "/info.json");

            return false;
        }

        $get_info = @json_decode(file_get_contents($dir . "/info.json"), true);

        $theme_name = @$get_info["theme_name"];
        $theme_author = @$get_info["theme_author"];
        $theme_zip_file_name = @$get_info["theme_zip_file_name"];
        $my_cms_version = @$get_info["my_cms_version"];
        $theme_error_page = @$get_info["theme_error_page"];
        $theme_maintenance_page = @$get_info["theme_maintenance_page"];
        $theme_version = @$get_info["theme_version"];
        $theme_languages = @$get_info["theme_languages"];

        if (empty($theme_name)) {
            define('INDEX_ERROR', 'Error Install - "Missing Theme Name"');

            return false;
        }
        if (empty($theme_author)) {
            define('INDEX_ERROR', 'Error Install - "Missing Theme Author"');

            return false;
        }
        if (empty($theme_zip_file_name)) {
            define('INDEX_ERROR', 'Error Install - "Missing Theme zip file name"');

            return false;
        }
        if (!file_exists($dir . "/" . $theme_zip_file_name . '.zip')) {
            define('INDEX_ERROR', 'Error Install - Can\'t find ' . $theme_zip_file_name . '.zip - [T]');

            return false;
        }
        if (empty($my_cms_version)) {
            define('INDEX_ERROR', 'Error Install - "Missing MyCMS Version"');

            return false;
        }
        if (empty($theme_error_page)) {
            define('INDEX_ERROR', 'Error Install - "Missing Error Page"');

            return false;
        }
        if (empty($theme_maintenance_page)) {
            define('INDEX_ERROR', 'Error Install - "Missing Maintenance Page"');

            return false;
        }
        if (empty($theme_version)) {
            define('INDEX_ERROR', 'Error Install - "Missing Theme version"');

            return false;
        }
        if (empty($theme_languages)) {
            define('INDEX_ERROR', 'Error Install - "Missing Theme languages"');

            return false;
        }
        if ($my_cms_version != $this->container['my_cms_version']) {
            define('INDEX_ERROR', 'Error Install - <b>Wrong MyCMS Version</b>');

            return false;
        }

        $themeFolder = "." . MY_BASE_PATH . "/src/App/Content/Theme/" . $theme_zip_file_name;

        if (file_exists($themeFolder)) {
            define('INDEX_ERROR', 'Error Install - <b>Another Theme with this name</b>');

            return false;
        }

        if (!mkdir($themeFolder, 0755, true)) {
            define('INDEX_ERROR', 'Error Install - <b>Can\'t create theme folder, check permission!</b>');

            return false;
        }

        $zip_extract = new ZipArchive;
        if ($zip_extract->open($dir . "/" . $theme_zip_file_name . '.zip') === true) {
            $zip_extract->extractTo($themeFolder);
            $zip_extract->close();
        } else {
            define('INDEX_ERROR', 'Error Install - <b>ZIP ERROR</b>');

            return false;
        }

        //$this->container['functions']->removeDir($dir);
        $this->container['functions']->removeDir("." . MY_BASE_PATH . "/tmp/");
        $this->container['database']->query("INSERT INTO my_style (style_name,style_author,style_path_name,style_error_page,style_maintenance_page,style_json_file_url,style_version,style_languages) VALUES (:style_name,:style_author,:style_path_name,:style_error_page,:style_maintenance_page,:style_json_file_url,:style_version,:style_languages)", ['style_name' => $theme_name, 'style_author' => $theme_author, 'style_path_name' => $theme_zip_file_name, 'style_error_page' => $theme_error_page, 'style_maintenance_page' => $theme_maintenance_page, 'style_json_file_url' => "local", 'style_version' => $theme_version, 'style_languages' => $theme_languages]);

        return true;
    }

    function downloadTheme($url)
    {
        $download_url = $url;
        $get_info = @json_decode(file_get_contents($download_url), true);

        $theme_name = @$get_info["theme_name"];
        $theme_author = @$get_info["theme_author"];
        $theme_zip_file_name = @$get_info["theme_zip_file_name"];
        $my_cms_version = @$get_info["my_cms_version"];
        $theme_error_page = @$get_info["theme_error_page"];
        $theme_maintenance_page = @$get_info["theme_maintenance_page"];
        $theme_version = @$get_info["theme_version"];
        $theme_languages = @$get_info["theme_languages"];

        if (empty($theme_name)) {
            return '<div class="alert alert-danger"> Error Download 1</div>';
        }
        if (empty($theme_author)) {
            return '<div class="alert alert-danger"> Error Download 2</div>';
        }
        if (empty($theme_zip_file_name)) {
            return '<div class="alert alert-danger"> Error Download 3</div>';
        }
        if (empty($my_cms_version)) {
            return '<div class="alert alert-danger"> Error Download 4</div>';
        }
        if (empty($theme_error_page)) {
            return '<div class="alert alert-danger"> Error Download 5</div>';
        }
        if (empty($theme_maintenance_page)) {
            return '<div class="alert alert-danger"> Error Download 6</div>';
        }
        if (empty($theme_version)) {
            return '<div class="alert alert-danger"> Error Download 7</div>';
        }
        if (empty($theme_languages)) {
            return '<div class="alert alert-danger"> Error Download 8</div>';
        }
        if ($my_cms_version != $this->container['my_cms_version']) {
            return '<div class="alert alert-danger"> Error Download - <b>Wrong MyCMS Version</b> </div>';
        }

        $zipname = $theme_zip_file_name . '.zip';

        if (file_exists(P_PATH_S . "/src/App/Content/Theme/" . $theme_zip_file_name)) {
            return '<div class="alert alert-danger"> Error Download - <b>Another Theme with this name</b> </div>';
        }

        ignore_user_abort(true);
        set_time_limit(0);

        $download_path = str_replace('/info.json', '', $download_url);


        if (!$info = file_get_contents($download_path . "/" . $zipname)) {
            return '<div class="alert alert-danger"> Error Download - <b>Can\'t find theme zip file [' . $zipname . ']</b> </div>';
        }

        $dir = "." . MY_BASE_PATH . "/tmp/" . $theme_zip_file_name;
        $real_path = "." . MY_BASE_PATH . "/src/App/Content/Theme/" . $theme_zip_file_name;

        if (!mkdir($dir, 0755, true)) {
            return '<div class="alert alert-danger"> Error Download - <b>Can\'t create tmp folder, check permission!</b> </div>';
        }

        if (!mkdir($real_path, 0755, true)) {
            return '<div class="alert alert-danger"> Error Download - <b>Can\'t create theme folder, check permission!</b> </div>';
        }

        file_put_contents($dir . '/' . $zipname, $info);

        $zip_extract = new ZipArchive;
        if ($zip_extract->open($dir . '/' . $zipname) === true) {
            $zip_extract->extractTo($dir . '/');
            $zip_extract->close();
        } else {
            return '<div class="alert alert-danger"> Error ZIP - <b>Can\'t Open, check Extension!</b> </div>';
        }

        unlink($dir . '/' . $zipname);

        $source = $dir . '/';
        $destination = $real_path . "/";
        $this->folderCopy($source, $destination);

        $this->container['functions']->removeDir("." . MY_BASE_PATH . "/tmp");
        $this->container['database']->query("INSERT INTO my_style (style_name,style_author,style_path_name,style_error_page,style_maintenance_page,style_json_file_url,style_version,style_languages) VALUES (:style_name,:style_author,:style_path_name,:style_error_page,:style_maintenance_page,:style_json_file_url,:style_version,:style_languages)", ['style_name' => $theme_name, 'style_author' => $theme_author, 'style_path_name' => $theme_zip_file_name, 'style_error_page' => $theme_error_page, 'style_maintenance_page' => $theme_maintenance_page, 'style_json_file_url' => $download_url, 'style_version' => $theme_version, 'style_languages' => $theme_languages]);

        return true;
    }

    function folderCopy($src, $dst)
    {
        if (file_exists($dst)) {
            $this->container['functions']->removeDir($dst);
        }

        if (is_dir($src)) {
            mkdir($dst);
            $files = scandir($src);
            foreach ($files as $file) {
                if ($file != "." && $file != "..") {
                    $this->folderCopy("$src/$file", "$dst/$file");
                }
            }

        } else if (file_exists($src)) {
            copy($src, $dst);
        }

    }

    public function thereIsNewUpdate($check = true)
    {

        $get_info = @json_decode(file_get_contents(MY_CMS_WEBSITE . "/update/update.json"), true);

        $my_cms_core_update = @$get_info["my_cms_core_update"];
        $my_cms_db_update = @$get_info["my_cms_db_update"];
        $my_cms_changelog_array = @$get_info["my_cms_changelog_array"];
        $my_cms_db_changelog_array = @$get_info["my_cms_db_changelog_array"];

        $cms_version = "";
        $db_version = "";
        $changelog_array = [];

        if ($my_cms_core_update != '' && $my_cms_db_update != '') {
            if (version_compare($my_cms_core_update, $this->container['my_cms_version'], '>') && version_compare($my_cms_db_update, $this->container['my_cms_db_version'], '>')) {
                $info = 'all_update';
                $cms_version = $my_cms_core_update;
                $db_version = $my_cms_db_update;
                $changelog_array = $my_cms_changelog_array;
                $changelog_array = array_merge($changelog_array, $my_cms_db_changelog_array);
                $return = true;
            } elseif (version_compare($my_cms_core_update, $this->container['my_cms_version'], '>')) {
                $info = 'core_update';
                $cms_version = $my_cms_core_update;
                $db_version = '';
                $changelog_array = $my_cms_changelog_array;
                $return = true;
            } elseif (version_compare($my_cms_db_update, $this->container['my_cms_db_version'], '>')) {
                $info = 'db_update';
                $cms_version = '';
                $db_version = $my_cms_db_update;
                $changelog_array = $my_cms_db_changelog_array;
                $return = true;
            } else {
                $info = '';
                $return = false;
            }
        } else {
            $info = '';
            $return = false;
        }

        if ($check) {
            return $return;
        } else {
            return [$return, $info, $cms_version, $db_version, $changelog_array];
        }
    }

    function startConsoleMode()
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 100000);
        set_time_limit(0);

        echo $this->consoleFirstText();
        define("MY_CMS_CONSOLE_MODE", true);

        //Show menu
        echo "Welcome to MyCMS Console Mode\n";
        echo "Type a command ('help' for list of commands)'\n";
        while (true) {
            echo "\n>";
            $command = trim(fgets(fopen("php://stdin", "r")));
            switch ($command) {
                case 'help':
                    echo "This is the list of all commands you can do:\n";
                    echo "  help ( list of command with hint )\n";
                    echo "  exit ( exit from my-cms console mode )\n";
                    echo "  mycms (-v for version, -dbv for database version )\n";
                    echo "  my-admin-mode ( enter in my-admin mode for manage the website)\n";

                    $consolePluginCommands = $this->getConsoleCommands();
                    foreach ($consolePluginCommands as $command => $value) {
                        if ($value["isAdminCommand"] == false) {
                            $description = (!empty($value["description"])) ? " ( " . $value["description"] . ")" : "";
                            echo "  " . $command . $description . " [p]\n";
                        }
                    }

                    break;
                case 'exit':
                    echo "Exit from MyCMS Console Mode...\n";
                    sleep(1);
                    exit();
                    break;
                case 'mycms':
                    echo "Please use mycms [-v | -dbv] only one for time\n";
                    break;
                case 'mycms -v':
                    echo "-------------------------------\n";
                    echo "MyCMS Version " . $this->container['my_cms_version'] . "\n";
                    echo "-------------------------------\n";
                    break;
                case 'mycms -dbv':

                    echo "-------------------------------\n";
                    echo "MyCMS Database Version " . $this->container['my_cms_db_version'] . "\n";
                    echo "-------------------------------\n";
                    break;
                case 'my-admin-mode':
                    echo "For use this mod please login with admin account\n";
                    $try = true;
                    $success = false;
                    while ($try == true) {
                        echo "Email:\n";
                        $email = trim(fgets(fopen("php://stdin", "r")));
                        echo "Password:\n";
                        $password = trim(fgets(fopen("php://stdin", "r")));

                        $mail = htmlentities($this->container['security']->mySqlSecure($email));
                        $password = htmlentities($this->container['security']->mySqlSecure($password));
                        $login = $this->container['users']->loginAdmin($mail, $password, false);
                        if ($login["login"] == 1) {
                            $success = true;
                            $try = false;
                        } else {
                            echo "\n";
                            echo ea($login["error"], '1') . "\n";
                            echo "\n";
                            $try = false;
                        }

                        if ($success == true && $try == false) {
                            echo "Success...!\n";
                            echo "\n";
                            $complete_name = $this->container['users']->getInfo($_SESSION['staff']['id'], 'name') . ' ' . $this->container['users']->getInfo($_SESSION['staff']['id'], 'surname');
                            $user_rank = $this->container['users']->getInfo($_SESSION['staff']['id'], 'rank');
                            if ($user_rank >= 3) {
                                $this->consoleAW("Welcome $complete_name\n");
                                $this->consoleAW("Type a command ('help' for list of commands)'\n");
                                $admin_mode = true;
                                while ($admin_mode == true) {
                                    echo "\n[" . $this->container['users']->getInfo($_SESSION['staff']['id'], 'name') . "]>";
                                    $command_admin = trim(fgets(fopen("php://stdin", "r")));
                                    switch ($command_admin) {
                                        case 'help':
                                            $this->consoleAW("This is the list of all commands you can do:\n");
                                            $this->consoleAW("    exit mode ( Exit from admin mode )\n");
                                            $this->consoleAW("    enable | disable ( -maintenance )\n");
                                            $this->consoleAW("    set ( -site_name | -site_description | -site_url )\n");

                                            $consolePluginCommands = $this->getConsoleCommands();
                                            foreach ($consolePluginCommands as $command => $value) {
                                                $description = (!empty($value["description"])) ? " ( " . $value["description"] . ")" : "";
                                                echo "" . $command . $description . " [p]\n";
                                            }

                                            break;
                                        case 'set -site_name':
                                            $this->consoleAW("Write name for website:\n");
                                            $site_name = htmlentities(fgets(fopen("php://stdin", "r")));
                                            if ($this->container['settings']->saveSettings('site_name', $site_name) == false) {
                                                $this->consoleAW(ea('error_page_settings_general_save', '1'));
                                            } else {
                                                $this->consoleAW("Site Name changed in $site_name !\n");
                                            }
                                            break;
                                        case 'set -site_description':
                                            $this->consoleAW("Write description for website:\n");
                                            $site_description = htmlentities(fgets(fopen("php://stdin", "r")));
                                            if ($this->container['settings']->saveSettings('site_description', $site_description) == false) {
                                                $this->consoleAW(ea('error_page_settings_general_save', '1'));
                                            } else {
                                                $this->consoleAW("Site Description changed in $site_description !\n");
                                            }
                                            break;
                                        case 'set -site_url':
                                            $this->consoleAW("Write url for website (Warning!!!):\n");
                                            $site_url = htmlentities(fgets(fopen("php://stdin", "r")));
                                            if ($this->container['settings']->saveSettings('site_url', $site_url) == false) {
                                                $this->consoleAW(ea('error_page_settings_general_save', '1'));
                                            } else {
                                                $this->consoleAW("Site Url changed in $site_url !\n");
                                            }
                                            break;
                                        case 'enable -maintenance':
                                            if ($this->container['settings']->saveSettings('site_maintenance', "true") == false) {
                                                $this->consoleAW(ea('error_page_settings_general_save', '1'));
                                            } else {
                                                $this->consoleAW("Maintenance enabled!\n");
                                            }
                                            break;
                                        case 'disable -maintenance':
                                            if ($this->container['settings']->saveSettings('site_maintenance', "false") == false) {
                                                $this->consoleAW(ea('error_page_settings_general_save', '1'));
                                            } else {
                                                $this->consoleAW("Maintenance disabled!\n");
                                            }
                                            break;
                                        case 'exit mode':
                                            $this->consoleAW("Exit from MyAdmin Mode..\n");
                                            sleep(1);
                                            $admin_mode = false;
                                            echo "Bye...\n";
                                            break;
                                        default:
                                            $consolePluginCommands = $this->getConsoleCommands();
                                            if (isset($consolePluginCommands[ $command ])) {
                                                $consolePluginCommands[ $command ]["function"]();
                                            } else {
                                                $this->consoleAW("Command not found! Please type 'help'\n");
                                            }
                                    }
                                }
                            } else {
                                $this->consoleAW("You are not admin (rank 3)\n");
                                sleep(1);
                                echo "Bye...\n";
                                break;
                            }
                        } else {
                            echo "Do you want retry? (y | n)\n";
                            $retry = trim(fgets(fopen("php://stdin", "r")));
                            if ($retry == 'y') {
                                $try = true;
                            } else {
                                echo "Bye...\n";
                                sleep(1);
                            }
                        }

                    }
                    break;
                default:

                    $consolePluginCommands = $this->getConsoleCommands();
                    if (isset($consolePluginCommands[ $command ]) && $consolePluginCommands[ $command ]["isAdminCommand"] == false) {
                        $consolePluginCommands[ $command ]["function"]();
                    } else {
                        echo "Command not found! Please type 'help'\n";
                    }

            }
        }
    }

    function consoleFirstText()
    {
        $string = "  __  __        _____ __  __  _____ \n";
        $string .= " |  \\/  |      / ____|  \\/  |/ ____|\n";
        $string .= " | \\  / |_   _| |    | \\  / | (___  \n";
        $string .= " | |\\/| | | | | |    | |\\/| |\\___ \\ \n";
        $string .= " | |  | | |_| | |____| |  | |____) |\n";
        $string .= " |_|  |_|\\__, |\\_____|_|  |_|_____/ \n";
        $string .= "          __/ |                     \n";
        $string .= "         |___/                      \n";
        $string .= "               Version " . $this->container['my_cms_version'] . "\n";
        $string .= "               Console Mode\n";

        return $string;
    }

    public function getConsoleCommands()
    {
        return $this->consolePluginCommands;
    }

    function consoleAW($str)
    {
        if (MY_CMS_CONSOLE_MODE == true) {
            echo "[MY-ADMIN] " . $str;
        }
    }

    public function my_page_export($page_id)
    {
        $page = $this->container['database']->row("SELECT * FROM my_page WHERE pageID = :page_id AND pageCANDELETE = '1'", ["page_id" => $page_id]);
        unset($page["pageID"]);
        $page["pageHTML"] = str_replace('"', "'", $page["pageHTML"]);
        ob_end_clean();

        $json_page = stripcslashes(json_encode($page, JSON_PRETTY_PRINT));
        header('Content-Description: File Transfer');
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="' . $page["pageTITLE"] . '.json"');
        echo $json_page;
        exit;
    }

    //deprecated

    function fixTheme($theme)
    {
        $theme_path = $this->getThemePath();
        if (file_exists($theme_path)) {
            return $theme;
        } else {
            return "default";
        }
    }

    function requirePage($bool, $page)
    {
        if ($bool == true) {
            if ($page != PAGE_ID) {
                header('Location: ' . HOST . '');
                exit;
            }
        }
    }

    function getMenu($return = false)
    {
        $returnMenu = "";

        $menu_query = $this->container['database']->query("SELECT * FROM my_menu WHERE menu_enabled = '1' ORDER BY menu_sort");
        foreach ($menu_query as $menu_row) {

            $menu_name = $menu_row['menu_name'];
            $menu_page_id = $menu_row['menu_page_id'];
            $menu_link = $menu_row['menu_link'];
            $menu_icon = $menu_row['menu_icon'];
            $menu_icon_image = $menu_row['menu_icon_image'];
            $menu_dropdown = $menu_row['menu_dropdown'];

            $set_icon = '';

            if ($menu_icon == 'fa') {
                $set_icon = '<i class="fa fa-' . $menu_icon_image . '"></i> ';
            } elseif ($menu_icon == 'glyphicon') {
                $set_icon = '<i class="glyphicon glyphicon-' . $menu_icon_image . '"></i> ';
            }

            if ($menu_dropdown == '0') {
                if (defined("PAGE_ID") && PAGE_ID == $menu_page_id) {
                    $returnMenu .= '<li class="active">';
                } else {
                    $returnMenu .= '<li>';
                }

                $returnMenu .= '<a href="' . $menu_link . '">' . $set_icon . $menu_name . '</a></li>';

            } else {
                if (defined("PAGE_ID") && PAGE_ID == $menu_page_id) {
                    $returnMenu .= '<li class="active dropdown">';
                } else {
                    $returnMenu .= '<li class="dropdown>';
                }

                $returnMenu .= '<a href="#" class="dropdown-toggle" data-toggle="dropdown">' . $menu_name . '<b class="caret"></b></a>';

                $returnMenu .= '<ul class="dropdown-menu">';

                $menu_dropdown_query = $this->container['database']->query("SELECT * FROM my_menu WHERE menu_dropdown_parent = '" . $menu_page_id . "'");

                while ($menu_dropdown_row = $this->container['database']->fetch($menu_dropdown_query)) {
                    $returnMenu .= '<li>';
                    $returnMenu .= '<a href="' . $menu_link . '">' . $set_icon . $menu_name . '</a>';
                    $returnMenu .= '</li>';
                }
                $returnMenu .= '</ul>';
                $returnMenu .= '</li>';

            }

        }

        if ($return) {
            return $returnMenu;
        }

        echo $returnMenu;

        return true;
    }

    function get_menu()
    {
        $menu_query = $this->container['database']->query("SELECT * FROM my_menu WHERE menu_enabled = '1' ORDER BY menu_sort");
        foreach ($menu_query as $menu_row) {

            $menu_name = $menu_row['menu_name'];
            $menu_page_id = $menu_row['menu_page_id'];
            $menu_link = $menu_row['menu_link'];
            $menu_icon = $menu_row['menu_icon'];
            $menu_icon_image = $menu_row['menu_icon_image'];
            $menu_dropdown = $menu_row['menu_dropdown'];

            if ($menu_icon == 'fa'):

                $set_icon = '<i class="fa fa-' . $menu_icon_image . '"></i> ';

            elseif ($menu_icon == 'glyphicon'):

                $set_icon = '<i class="glyphicon glyphicon-' . $menu_icon_image . '"></i> ';

            else:

                $set_icon = '';

            endif;

            if ($menu_dropdown == '0'):
                ?>

                <li <?php if (defined("PAGE_ID") && PAGE_ID == $menu_page_id) {
                    echo 'class="active"';
                } ?>><a href="<?php echo $menu_link; ?>"><?php echo $set_icon; ?><?php echo $menu_name; ?></a></li>

                <?php
            else:
                ?>

                <li <?php if (defined("PAGE_ID") && PAGE_ID == $menu_page_id) {
                    echo 'class="active"';
                } ?> class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $menu_name; ?> <b
                                class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <?php
                        $menu_dropdown_query = $this->container['database']->query("SELECT * FROM my_menu WHERE menu_dropdown_parent = '" . $menu_page_id . "'");
                        while ($menu_dropdown_row = $this->container['database']->fetch($menu_dropdown_query)) {
                            ?>
                            <li>
                                <a href="<?php echo $menu_link; ?>"><?php echo $set_icon; ?><?php echo $menu_name; ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                </li>

                <?php
            endif;

        }

    }

    /**
     * Add command to the console
     * @param $command
     * @param $function
     * @param bool $isAdminCommand
     * @param string $description
     * @return int
     */
    public function addConsoleCommand($command, $function, $isAdminCommand = false, $description = "")
    {
        if (is_callable($function)) {
            if (!isset($this->consolePluginCommands[ $command ])) {
                $this->consolePluginCommands[ $command ] = ["function" => $function, "isAdminCommand" => $isAdminCommand, "description" => $description];

                return 2;
            }

            return 1;
        }

        return 0;
    }

    public function getThemeSetting($name)
    {
        $settings = $this->getThemeSettings();

        if (isset($settings[ $name ])) {
            return $settings[ $name ];
        }

        return false;
    }

    public function setThemeSetting($name, $value, $default = false)
    {
        $settings = $this->getThemeSettings();

        $change = false;

        if ((!isset($settings[ $name ]) || $settings[ $name ] != $value) && $default == false) {
            $settings[ $name ] = $value;
            $change = true;
        } else if (!isset($settings[ $name ]) && $default == true) {
            $settings[ $name ] = $value;
            $change = true;
        }

        if ($change == false) {
            return true;
        } else {
            return $this->setThemeSettings($settings);
        }

    }

    public function setThemeSettings($settings, $theme = "")
    {
        if (empty($theme)) {
            $theme = MY_THEME;
        }

        $settings = base64_encode(serialize($settings));

        $info = $this->container["settings"]->addSettingsValue("theme_settings_$theme", $settings);
        if ($info === false) {
            return $this->container["settings"]->saveSettings("theme_settings_$theme", $settings);
        }

        return true;
    }

    //todo add all useful function to plugin system
}
