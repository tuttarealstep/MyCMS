<?php
/**
 * User: tuttarealstep
 * Date: 10/04/16
 * Time: 11.34
 */

namespace MyCMS\App\Utils\PageLoader;

use Twig_Environment;
use Twig_Loader_Filesystem;

class MyCMSPageLoader
{
    private $container;

    private $twig_enabled = false;

    function __construct($container)
    {
        $this->container = $container;

        if (defined("ENABLE_TWIG_TEMPLATE_ENGINE")) {
            if (ENABLE_TWIG_TEMPLATE_ENGINE) {
                $this->twig_enabled = true;
            }
        }
    }

    function loadDatabasePage($get_url)
    {
        $timer_start = microtime(true);

        if (isset($get_url)) {
            $url = $get_url;
            $styleInfo = $this->container['theme']->styleInfo(MY_THEME);
            if ($get_url == $styleInfo["style_error_page"]) {
                $url = $this->container['functions']->fixText(htmlspecialchars(substr("$_SERVER[REQUEST_URI]", 1)));
            }
        } else {
            $url = $this->container['functions']->fixText(htmlspecialchars(substr("$_SERVER[REQUEST_URI]", 1)));
        }

        $base_path = str_replace('/', '', MY_BASE_PATH);
        $url = str_replace($base_path, '', $url);
        $url = str_replace('/', '', $url);

        if (empty($url))
            return false;

        if (isset($_SESSION['user']['id'])) {
            if (!$this->container['database']->iftrue("SELECT pageId FROM my_page WHERE pageUrl = :page_url OR pageUrl = :page_url_two AND pageInTheme = '0' LIMIT 1", ["page_url" => $url, "page_url_two" => "{@siteURL@}/" . $url])) {
                return false;
            }
            $info = $this->container['database']->row("SELECT * FROM my_page WHERE pageUrl = :page_url OR pageUrl = :page_url_two AND pageInTheme = '0' LIMIT 1", ["page_url" => $url, "page_url_two" => "{@siteURL@}/" . $url]);
            if (!isset($info)) {
                return false;
            }
        } else {
            if (!$this->container['database']->iftrue("SELECT pageId FROM my_page WHERE pageUrl = :page_url OR pageUrl = :page_url_two AND pagePublic = '1' AND pageInTheme = '0' LIMIT 1", ["page_url" => $url, "page_url_two" => "{@siteURL@}/" . $url])) {
                return false;
            }
            $info = $this->container['database']->row("SELECT * FROM my_page WHERE pageUrl = :page_url OR pageUrl = :page_url_two AND pagePublic = '1' AND pageInTheme = '0' LIMIT 1", ["page_url" => $url, "page_url_two" => "{@siteURL@}/" . $url]);
            if (!isset($info)) {
                return false;
            }
        }


        define('PAGE_ID', $info["pageIdMenu"]);
        define('PAGE_NAME', $this->container['functions']->removeSpace($info["pageTitle"]));

        if (isset($_SESSION["customizer"]) && $_SESSION["customizer"] == true) {
            $customizer = "<link rel=\"stylesheet\" href=\"{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/medium-editor/css/medium-editor.css\"><link rel=\"stylesheet\" href=\"{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/medium-editor/css/medium-editor-tables.css\"><link rel=\"stylesheet\" href=\"{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/medium-editor/css/themes/custom.css\" id=\"medium-editor-theme\">";
            $customizerAppend = "<script id=\"js_to_ex\" src=\"{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/medium-editor/js/medium-editor.js\"></script><script type=\"text/javascript\" src=\"{@MY_ADMIN_TEMPLATE_PATH@}/Assets/Plugins/medium-editor/js/medium-editor-tables.js\"></script>
<script>
if(typeof(parent.customizerPage) !== 'undefined' && parent.customizerPage == true)
    {
var editor = new MediumEditor('#customizer', {
    extensions: {
      table: new MediumEditorTable()
    },
    toolbar: {
        buttons: ['bold', 'italic', 'underline', 'anchor', 'h2', 'h3', 'quote', 'table']
    }
});
window.onload = function() 
{
    parent.enableMyPageCustomizer();
    var links = document.getElementsByTagName(\"a\");
    for (var i = 0; i < links.length; i++) 
    {
        links[i].onclick = function() {
            var confirm_v = confirm('" . $this->container['languages']->ta("page_admin_theme_customize_change_page", true) . "');
            if(confirm_v == true)
            {
                return(true);
            }
            return(false);
        };
    }
};
        
    }
</script>";
            $info["pageHtml"] = $customizer . "<input type='hidden' id='customizerPageId' value='" . $info["pageId"] . "'><span id='customizer'>" . $info["pageHtml"] . "</span>" . $customizerAppend;

        }

        if ($this->twig_enabled == false && $this->container['theme']->getStyleInfo("require_twig_engine") == true) {
            //TODO send notify - enable twig
        }

        if ($this->twig_enabled == true && $this->container['theme']->getStyleInfo("require_twig_engine") == false) {
            $this->twig_enabled = false;
        }

        if ($this->twig_enabled) {
            if ($this->container['theme']->getFile('page_loader', "", false, true) == "twig") {
                $page_array = [];

                if (defined('INDEX_ERROR')) {
                    $errors = ['INDEX_ERROR' => $this->container['theme']->indexErrorStyle_array["start_tag"] . INDEX_ERROR . $this->container['theme']->indexErrorStyle_array["finish_tag"]];
                } else {
                    $errors = ['INDEX_ERROR' => ''];
                }

                $page_array["page_loader_content"] = $info["pageHtml"] . "\n";
                $page_array["page_custom_css"] = $info["pageCustomCss"] . "\n";
                $page_array = array_merge($page_array, $this->container['theme']->tag);
                $page_array = array_merge($page_array, $errors);

                $twig_loader_filesystem = new Twig_Loader_Filesystem($this->container['theme']->getThemePath() . "/");

                if (MY_M_DEBUG || ENABLE_TWIG_TEMPLATE_DEBUG) {
                    if ($this->container['settings']->getSettingsValue('site_use_cache') == 'true') {
                        $twig_environment_array = [
                            'cache'       => C_PATH . '/Storage/Cache',
                            'auto_reload' => true,
                            'debug'       => true,
                            'autoescape'  => false
                        ];
                    } else {
                        $twig_environment_array = [
                            'auto_reload' => true,
                            'debug'       => true,
                            'autoescape'  => false
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

                $twig = new Twig_Environment($twig_loader_filesystem, $twig_environment_array);
                $twig->addExtension(new \Twig_Extension_Debug());
                $twig->addGlobal("container", $this->container);


                $page = $twig->render("page_loader.twig", $page_array);
                $page = str_replace("{@page_loader_content@}", $info["pageHtml"] . "\n", $page);
                $page = $this->container['theme']->parseNoTag($page);
                $page = $this->container['theme']->setTagFunctions($page);
            } else {
                $page = $this->container['theme']->getFile('page_loader', "", true) . "\n";
                $page = str_replace("{@page_loader_content@}", $info["pageHtml"] . "\n", $page);
                $page = $this->container['theme']->parseNoTag($page);
                $page = $this->container['theme']->setTagFunctions($page);
            }
        } else {
            $page = $this->container['theme']->getFile('page_loader', "", true) . "\n";
            $page = str_replace("{@page_loader_content@}", $info["pageHtml"] . "\n", $page);
            $page = $this->container['theme']->parseNoTag($page);
            $page = $this->container['theme']->setTagFunctions($page);
        }

        $page = str_replace("{@page_loader_title@}", (isset($info["PAGE_NAME"])) ? $info["PAGE_NAME"] : "{@siteNAME@}" . "\n", $page);
        $page = str_replace("{@page_custom_css@}", $info["pageCustomCss"] . "\n", $page);

        $finished = number_format(microtime(true) - $timer_start, 6);
        if (defined("MY_M_DEBUG") && MY_M_DEBUG == true) {
            $page .= "\n<!-- MyCMS Page Loader - Page loaded in " . $finished . " sec. -->";
        }
        if ($this->container['theme']->small_page == true) {
            $page = $this->container['functions']->removeSpace($page);
        }

        echo $page;

        return true;
    }

    function page_loader_match_database_page()
    {
        $info = $this->container['database']->query("SELECT pageUrl FROM my_page WHERE pageInTheme = '0'");

        foreach ($info as $tag_info) {
            $url_info = str_replace("{@siteURL@}", "", $tag_info);
            $name_info = str_replace("{@siteURL@}/", "", $tag_info);
            $this->container['router']->map('GET', $url_info, $name_info);

            $this->container['router']->map('GET', $url_info["pageUrl"] . "/[*:args]", $name_info["pageUrl"]);
        }
    }

    function checkIfPageExist($pageId)
    {
        if ($this->container['database']->iftrue("SELECT pageId FROM my_page WHERE pageId = :pageId AND pageInTheme = '0' LIMIT 1", ["pageId" => $pageId])) {
            return true;
        }

        return false;
    }
}
