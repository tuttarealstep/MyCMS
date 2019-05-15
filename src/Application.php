<?php
/**
 * MyCMS
 */

namespace MyCMS;

if (!defined("MY_CMS_PATH")) {
    die("NO SCRIPT");
}

use AltoRouter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use MyCMS\App\Utils\Admin\MyCMSAdmin;
use MyCMS\App\Utils\Api\MyCMSApi;
use MyCMS\App\Utils\Blog\MyCMSBlog;
use MyCMS\App\Utils\Database\MyCMSDatabase;
use MyCMS\App\Utils\Exceptions\MyCMSException;
use MyCMS\App\Utils\Facilities\MyCMSFunctions;
use MyCMS\App\Utils\Facilities\MyCMSThemeFunctions;
use MyCMS\App\Utils\Helper\MyCMSCron;
use MyCMS\App\Utils\Languages\MyCMSLanguage;
use MyCMS\App\Utils\Management\MyCMSCache;
use MyCMS\App\Utils\Users\MyCMSRoles;
use MyCMS\App\Utils\Users\MyCMSUsers;
use MyCMS\App\Utils\Media\MyCMSMedia;
use MyCMS\App\Utils\PageLoader\MyCMSPageLoader;
use MyCMS\App\Utils\Plugins\MyCMSPlugins;
use MyCMS\App\Utils\Router\MyCMSRouter;
use MyCMS\App\Utils\Security\MyCMSSecurity;
use MyCMS\App\Utils\Settings\MyCMSSettings;
use MyCMS\App\Utils\Theme\MyCMSTheme;
use MyCMS\App\Utils\Theme\MyCMSThemeCustomizer;

class Application
{
    /**
     * Container variable, the variable who contain all classes
     * @var array<MyCMSDatabase|MyCMSTheme|MyCMSCache|MyCMSPlugins|MyCMSThemeCustomizer|MyCMSUsers|MyCMSSettings|MyCMSSecurity|MyCMSFunctions>
     */
    public $container;

    private $initialized = false;

    private $activatePlugin = ["checkMyCMSPermissions", "simpleMarkdown"];

    /**
     * Application constructor.
     * Set two global variables, my_cms_version and my_cms_db_version
     * Next call the initialize function
     */
    function __construct()
    {
        $this->container['my_cms_version'] = '0.0.6.5';
        $this->container['my_cms_db_version'] = '0.0.1';

        $this->initialize();
    }

    /**
     * Load all classes of MyCMS
     * @throws MyCMSException
     */
    function initialize()
    {
        if ($this->isInConsole()) {
            define("LOADER_LOAD_PAGE", false);
        } else {
            if (!defined("LOADER_LOAD_PAGE")) {
                define("LOADER_LOAD_PAGE", true);
            }
        }

        if (isset($_SESSION['customizerLastAction']) && $_SESSION['customizerLastAction'] < time() - 30) {
            unset($_SESSION['customizerLastAction']);
            $_SESSION["customizer"] = false;
        }

        $this->container['pluginsManualActivate'] = $this->activatePlugin;

        $this->initErrorReporting();

        /* MyCMS 6 use Monolog */
        $this->initLogger();

        $this->initDatabase();

        $this->initCache();

        $this->initSettings();

        $this->initDefines();

        $this->initChecks();

        $this->initRouter();

        $this->initSecurity();

        $this->initPlugins();

        $this->initFunctions();

        $this->container['MyCMSRouter'] = new MyCMSRouter($this->container);

        $this->initLanguages();

        $this->initThemeCustomizer();

        $this->initTheme();

        $this->initRoles();

        $this->initUsers();

        $this->initMedia();

        $this->setTags();

        $this->initBlog();

        // Loading All Functions
        if (!defined("NO_THEME_FUNCTIONS")) {
            $this->container['theme']->loadThemeFunctions();
        }

        $this->container['theme']->loadAdminFunctions();

        $this->initThemeFunctions();

        $this->initAdmin();

        $this->initPageLoader();

        $this->initApi();

        $this->initScheduler();

        $this->updatePluginContainer();

        $this->initPluginsInitializedEvent();

        $dbActivePlugins = $this->container['settings']->getSettingsValue('active_plugins');
        if($dbActivePlugins != false)
        {
            $dbActivePlugins = unserialize(base64_decode($dbActivePlugins));
            if(is_array($dbActivePlugins))
            {
                $this->activatePlugin = array_merge($this->activatePlugin, $dbActivePlugins);
            }
            foreach ($this->activatePlugin as $plugin) {
                $this->container["plugins"]->activatePlugin($plugin);
            }
        }

        $this->container['plugins']->applyEvent('initialized');

        $this->initialized = true;
    }

    /**
     * Check if you run MyCMS trough console.
     * @return bool
     */
    function isInConsole()
    {
        if (php_sapi_name() == 'cli') {
            return true;
        }

        return false;
    }

    /**
     * Initialize the error reporting based on config,
     * if in the config you enable the debug mode, this report all errors.
     */
    function initErrorReporting()
    {
        if (MY_M_DEBUG === false) {
            error_reporting(E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_PARSE | E_USER_ERROR | E_USER_WARNING);
            ini_set('display_errors', 'On');
        } else {
            error_reporting(E_ALL);
            ini_set('display_errors', 'On');
        }
    }

    /**
     * Initialize Monolog class, and put in the container
     */
    function initLogger()
    {
        $logger = new Logger('MyCMS');
        $logger->pushHandler(new StreamHandler(C_PATH . '/Storage/Logs/Application.log'));
        $this->container['logger'] = $logger;
    }

    /**
     * Initialize the database.
     * @throws MyCMSException
     */
    function initDatabase()
    {
        $database = new MyCMSDatabase($this->container['logger']);
        $database->connect();
        $this->container['database'] = $database;
    }

    function initCache()
    {
        $cacheSystem = new MyCMSCache($this->container);
        $this->container['cache'] = $cacheSystem;
    }

    /**
     * Load the class which can load and save settings from or/to database.
     */
    function initSettings()
    {
        $this->container['settings'] = new MyCMSSettings($this->container['database'], $this->container['logger']);
    }

    /**
     * Set the remaining defines with the settings saved on the database.
     * timezone, host url, theme...
     */
    function initDefines()
    {

        if (!defined('MY_TIMEZONE')) {
            $site_timezone = $this->container['settings']->getSettingsValue('site_timezone');
            if (!empty($site_timezone)) {
                define('MY_TIMEZONE', $site_timezone);
            } else {
                define('MY_TIMEZONE', 'UTC');
            }
        }

        date_default_timezone_set(MY_TIMEZONE);

        if (!defined('HOST')) {
            $site_url = $this->container['settings']->getSettingsValue('site_url');
            if (!empty($site_url)) {
                define('HOST', $site_url);
            } else {
                define('HOST', 'http://' . $_SERVER['SERVER_NAME']);
            }
        }

        if (isset($_SESSION["user"]["id"])) {
            if (isset($_SESSION['customizerLastAction']) && isset($_GET["customizerTHEME"])) {
                if (!defined('MY_THEME')) {
                    define('MY_THEME', $_GET["customizerTHEME"]);
                }
            } elseif (isset($_SESSION['customizerThemeSession']['theme'])) {
                if (!defined('MY_THEME')) {
                    define('MY_THEME', $_SESSION['customizerThemeSession']['theme']);
                }
            }
        } else {
            if (!defined('MY_THEME')) {
                define('MY_THEME', $this->container['settings']->getSettingsValue('site_template'));
            }
        }

        if (!defined('MY_THEME')) {
            define('MY_THEME', $this->container['settings']->getSettingsValue('site_template'));
        }

        if (!defined("MY_BASE_PATH")) {
            define("MY_BASE_PATH", "");
        }

        if (!defined('MY_M_DEBUG')) {
            define('MY_M_DEBUG', false);
        }

        if (!defined('MY_MEMORY_LIMIT')) {
            define('MY_MEMORY_LIMIT', '64M');
        }

        @ini_set('memory_limit', MY_MEMORY_LIMIT);

        if (SESSION_KEY_GENERATE == true) {
            $_SESSION['security']['session_key'] = SESSION_KEY;
        }


    }

    /**
     * Check the session key, this is a particular security function.
     * @throws MyCMSException
     */
    function initChecks()
    {
        if (SESSION_KEY_GENERATE == false) {
            if ($_SESSION['security']['session_key'] != SESSION_KEY) {
                throw new MyCMSException('You no have access');
            }
        }
    }

    /**
     * Initialize the router which can route all request.
     */
    function initRouter()
    {
        $this->container['router'] = new AltoRouter();
    }

    /**
     * Load the security class and check the https.
     */
    function initSecurity()
    {
        $this->container['security'] = new MyCMSSecurity($this->container['settings']);
        $this->container['security']->myControlHttps();
    }

    function initPlugins()
    {
        $this->container['plugins'] = new MyCMSPlugins($this->container);
    }

    /**
     * Load the function class, who contain useful functions.
     */
    function initFunctions()
    {
        $this->container['functions'] = new MyCMSFunctions($this->container);
    }

    /**
     * Initialize the languages class which can load translation.
     */
    function initLanguages()
    {
        $this->container['languages'] = new MyCMSLanguage($this->container);
    }

    /**
     * Initialize the theme class
     */
    function initThemeCustomizer()
    {
        $this->container['themeCustomizer'] = new MyCMSThemeCustomizer($this->container);
    }

    /**
     * Initialize the theme class
     */
    function initTheme()
    {
        $this->container['theme'] = new MyCMSTheme($this->container);
        $this->container['themeCustomizer']->setContainer($this->container);
    }

    /**
     * Initialize the roles class
     */
    function initRoles()
    {
        $this->container['roles'] = new MyCMSRoles($this->container);
    }

    /**
     * Initialize the users class
     * @throws MyCMSException
     */
    function initUsers()
    {
        $users = new MyCMSUsers($this->container);

        /*
         * Support for HTML Themes (Conditional functions)
         *
         *
         * Example of use:
         * {@userLoggedIn=start@}
         *      Logged in
         * {@userLoggedIn=end@}
         *
         * {@userNotLoggedIn=start@}
         *      Logged out
         * {@userNotLoggedIn=end@}
         */
        $this->container['theme']->addFunctionsTag("{@userLoggedIn=start@}", "{@userLoggedIn=end@}", function ($content) use ($users)
        {
            if($users->userLoggedIn())
            {
                return $content;
            }

            return "";
        });

        $this->container['theme']->addFunctionsTag("{@userNotLoggedIn=start@}", "{@userNotLoggedIn=end@}", function ($content) use ($users)
        {
            if($users->userNotLoggedIn())
            {
                return $content;
            }

            return "";
        });

        /*
         * Support for HTML Themes (Conditional functions)
         *
         * Hide page with {@hidePageIfLogged@} if logged in
         * Hide page with {@hidePageIfNotLogged@} if not logged in
         */
        $this->container['theme']->addCallBackTag("hidePageIfLogged", function () use ($users)
        {
            $users->hideIfLogged();
        });

        $this->container['theme']->addCallBackTag("hidePageIfNotLogged", function () use ($users)
        {
            $users->hideIfNotLogged();
        });


        $users->controlBan();
        $users->controlSession();
        $users->setUserTag();

        $this->container['users'] = $users;
    }

    /**
     * Set all default theme tag
     */
    function setTags()
    {
        $this->container['theme']->addTag('siteNAME', $this->container['settings']->getSettingsValue('site_name')); //use {@siteNAME@} in your page
        $this->container['theme']->addTag('my_cms_version', $this->container['my_cms_version']);
        $this->container['theme']->addTag('my_php_version', phpversion());
        //todo finish or delete(deprecated)
        $this->container['theme']->addTag('my_mysql_version', '5.5'); //<-- this
        $this->container['theme']->addTag('siteURL', $this->container['settings']->getSettingsValue('site_url'));
        $this->container['theme']->addTag('siteTEMPLATE', $this->container['theme']->fixTheme(MY_THEME));
        //$this->container['theme']->addTag('siteTEMPLATE', $this->container['theme']->fixTheme($this->container['settings']->getSettingsValue('site_template')));
        $this->container['theme']->addTag('MY_ADMIN_TEMPLATE_PATH', $this->container['settings']->getSettingsValue('site_url') . MY_ADMIN_TEMPLATE_PATH);
        $this->container['theme']->addTag('MY_PLUGINS_PATH', $this->container['settings']->getSettingsValue('site_url') . MY_PLUGINS_PATH);
        $this->container['theme']->addTag('siteTIMEZONE', $this->container['settings']->getSettingsValue('site_timezone'));
        $this->container['theme']->addTag('siteLANGUAGE', $this->container['settings']->getSettingsValue('site_language'));
        $this->container['theme']->addTag('siteDESCRIPTION', $this->container['settings']->getSettingsValue('site_description'));
        $this->container['theme']->addTag('templateNAME', $this->container['theme']->getStyleInfo('name'));
        $this->container['theme']->addTag('templateVERSION', $this->container['theme']->getStyleInfo('version'));
        $this->container['theme']->addTag('templateAUTHOR', $this->container['theme']->getStyleInfo('author'));
        $this->container['theme']->addTag('templateCMS_VERSION', $this->container['theme']->getStyleInfo('cms_version'));
    }

    /**
     * Initialize the blog class
     */
    function initBlog()
    {
        $this->container['blog'] = new MyCMSBlog($this->container);
        $this->container['theme']->setContainer($this->container);
        $this->container['blog']->initBlogTags();

    }

    /**
     * Load the theme functions and settings.
     */
    function initThemeFunctions()
    {
        $ThemeFunctions = new MyCMSThemeFunctions($this->container['theme']);
        $ThemeFunctions->setThemeTags();

        $this->container['theme_functions'] = $ThemeFunctions;
    }

    /**
     * Set the admin routes
     */
    function initAdmin()
    {

        $this->container['my_admin'] = new MyCMSAdmin($this->container);
        $this->container['my_admin']->initRoutes();
        $this->container['my_admin']->checkNotification();
        $this->container['my_admin']->initPlugins();
        $this->container['theme']->setContainer($this->container);
    }

    /**
     * Initialize the page Loader which can load page from database (MyPage)
     */
    function initPageLoader()
    {
        $page_loader = new MyCMSPageLoader($this->container);
        $page_loader->page_loader_match_database_page();
        $this->container['page_loader'] = $page_loader;
    }

    /**
     * Load the api class
     */
    function initApi()
    {
        $this->container['api'] = new MyCMSApi($this->container);
    }

    /**
     * Load cron class
     *
     * todo call update time
     */
    function initScheduler()
    {
        $this->container['cron'] = new MyCMSCron($this->container);
    }

    function initMedia()
    {
        $this->container['media'] = new MyCMSMedia($this->container);
    }

    function updatePluginContainer()
    {
        $this->container["plugins"]->setContainer($this->container);
    }

    function initPluginsInitializedEvent()
    {

        $this->container['plugins']->addEvent('mimeTypes', (object)$this->container['security']->getMimeTypes());
        $this->container['themeCustomizer']->applyCustomizerLateEvents();
        $this->container['media']->setMediaEvents();
        $this->container['plugins']->addEvent('initialized', '');
    }

    /**
     * Return the requested page to the user.
     */
    function run()
    {
        $this->sendResponse();
    }

    /**
     * Load the page and send response to the user.
     */
    function sendResponse()
    {
        if (!$this->initialized)
            return;

        if ($this->container['api']->isApi()) {
            $this->container['api']->showApi();
        }

        $this->container['router']->setBasePath(MY_BASE_PATH);
        $match = $this->container['router']->match();

        $this->container['theme']->addTag('my_cms_welcome_h1', $this->container['languages']->e('my_cms_welcome_h1', true));

        if ($this->isInConsole()) {
            $this->container['theme']->startConsoleMode();
        }

        if (defined("LOADER_LOAD_PAGE") && LOADER_LOAD_PAGE == true) {

            if (empty($match['target'])) {
                $styleInfo = $this->container['theme']->styleInfo(MY_THEME);
                $match['target'] = $styleInfo["style_error_page"];
            }


            $this->container['plugins']->applyEvent('custom_headers');

            if ($this->container['theme']->isAdminUrl($match['target']) == false) {
                $this->container['theme']->controlMaintenance($match['target']);
                $info = $this->container['page_loader']->loadDatabasePage($match['target']);
                if ($info == false) {
                    if (!isset($match['params'])) {
                        $match['params'] = [];
                    }
                    $this->container['theme']->loadTheme($match['target'], $match['params']);
                }
            } else {
                $this->container['theme']->adminLoadTheme($match['target'], $match['params']);
            }

        }
    }

    /**
     * Shutdown Handler, throw MyCMS Exception a beautiful view for errors.
     * @throws MyCMSException
     */
    function fatalErrorShutdownHandler()
    {
        $last_error = error_get_last();
        if ($last_error['type'] === E_ERROR) {
            // fatal error
            throw new MyCMSException(E_ERROR, $last_error['message'], $last_error['file'], $last_error['line']);
        }
    }
}
