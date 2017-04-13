<?php
    /**
     * MyCMS
     */

    namespace MyCMS;

    if (!defined("MY_CMS_PATH")) {
        die("NO SCRIPT");
    }

    use Monolog\Handler\StreamHandler;
    use Monolog\Logger;
    use MyCMS\App\Utils\Admin\MyCMSAdmin;
    use MyCMS\App\Utils\Api\MyCMSApi;
    use MyCMS\App\Utils\Blog\MyCMSBlog;
    use MyCMS\App\Utils\Database\MyCMSDatabase;
    use MyCMS\App\Utils\Exceptions\MyCMSException;
    use MyCMS\App\Utils\Facilities\MyCMSFunctions;
    use MyCMS\App\Utils\Facilities\MyCMSThemeFunctions;
    use MyCMS\App\Utils\Languages\MyCMSLanguage;
    use MyCMS\App\Utils\Management\MyCMSCache;
    use MyCMS\App\Utils\Management\MyCMSUsers;
    use MyCMS\App\Utils\PageLoader\MyCMSPageLoader;
    use MyCMS\App\Utils\Plugins\MyCMSPlugins;
    use MyCMS\App\Utils\Router\AltoRouter;
    use MyCMS\App\Utils\Router\MyCMSRouter;
    use MyCMS\App\Utils\Security\MyCMSSecurity;
    use MyCMS\App\Utils\Settings\MyCMSSettings;
    use MyCMS\App\Utils\Theme\MyCMSTheme;
    use MyCMS\App\Utils\Theme\MyCMSThemeCustomizer;

    class Application
    {
        /**
         * Container variable, the variable who contain all classes
         * @var
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
            $this->container['my_cms_version'] = '0.0.6.1';
            $this->container['my_cms_db_version'] = '0.0.1';

            $this->initialize();
        }

        /**
         * Return the requested page to the user.
         */
        function run()
        {
            $this->send_response();
        }

        /**
         * Load all classes of MyCMS
         * @throws MyCMSException
         */
        function initialize()
        {
            if ($this->is_in_console()) {
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

            $this->init_error_reporting();

            /* MyCMS 6 use Monolog */
            $this->init_logger();

            $this->init_database();

            $this->init_cache();

            $this->init_settings();

            $this->init_defines();

            $this->init_checks();

            $this->init_router();

            $this->init_security();

            $this->initPlugins();

            $this->init_functions();

            $this->container['MyCMSRouter'] = new MyCMSRouter($this->container);

            $this->init_languages();

            $this->initThemeCustomizer();

            $this->init_theme();

            $this->init_users();

            $this->set_tags();

            $this->init_blog();

            // Loading All Functions
            if (!defined("NO_THEME_FUNCTIONS")) {
                $this->container['theme']->load_theme_functions();
            }

            $this->container['theme']->load_admin_functions();

            $this->init_theme_functions();

            $this->init_admin();

            $this->init_page_loader();

            $this->init_api();

            $this->updatePluginContainer();

            $this->initPluginsInitializedEvent();

            foreach ($this->activatePlugin as $plugin) {
                $this->container["plugins"]->activatePlugin($plugin);
            }

            $this->container['plugins']->applyEvent('initialized');

            $this->initialized = true;
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

        /**
         * Initialize the error reporting based on config,
         * if in the config you enable the debug mode, this report all errors.
         */
        function init_error_reporting()
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
        function init_logger()
        {
            $logger = new Logger('MyCMS');
            $logger->pushHandler(new StreamHandler(C_PATH . '/Storage/Logs/Application.log'));
            $this->container['logger'] = $logger;
        }

        /**
         * Initialize the database.
         * @throws MyCMSException
         */
        function init_database()
        {
            $database = new MyCMSDatabase($this->container['logger']);
            $database->Connect();
            $this->container['database'] = $database;
        }

        function init_cache()
        {
            $cacheSystem = new MyCMSCache($this->container);
            $this->container['cache'] = $cacheSystem;
        }

        /**
         * Check the session key, this is a particular security function.
         * @throws MyCMSException
         */
        function init_checks()
        {
            if (SESSION_KEY_GENERATE == false) {
                if ($_SESSION['security']['session_key'] != SESSION_KEY) {
                    throw new MyCMSException('You no have access');
                }
            }
        }

        /**
         * Load the class which can load and save settings from or/to database.
         */
        function init_settings()
        {
            $this->container['settings'] = new MyCMSSettings($this->container['database'], $this->container['logger']);
        }

        /**
         * Set the remaining defines with the settings saved on the database.
         * timezone, host url, theme...
         */
        function init_defines()
        {

            if (!defined('MY_TIMEZONE')) {
                $site_timezone = $this->container['settings']->get_settings_value('site_timezone');
                if (!empty($site_timezone)) {
                    define('MY_TIMEZONE', $site_timezone);
                } else {
                    define('MY_TIMEZONE', 'UTC');
                }
            }

            date_default_timezone_set(MY_TIMEZONE);

            if (!defined('HOST')) {
                $site_url = $this->container['settings']->get_settings_value('site_url');
                if (!empty($site_url)) {
                    define('HOST', $site_url);
                } else {
                    define('HOST', 'http://' . $_SERVER['SERVER_NAME']);
                }
            }

            if (isset($_SESSION["staff"]["id"])) {
                if (isset($_SESSION['customizerLastAction']) && isset($_GET["customizerTHEME"])) {
                    if (!defined('MY_THEME')) {
                        define('MY_THEME', $_GET["customizerTHEME"]);
                    }
                } elseif (isset($_SESSION['customizerThemeSession']['theme'])) {
                    if (!defined('MY_THEME')) {
                        define('MY_THEME', $_SESSION['customizerThemeSession']['theme']);
                    }
                }
            }

            if (!defined('MY_THEME')) {
                define('MY_THEME', $this->container['settings']->get_settings_value('site_template'));
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
         * Check if you run MyCMS trough console.
         * @return bool
         */
        function is_in_console()
        {
            if (php_sapi_name() == 'cli') {
                return true;
            }

            return false;
        }

        /**
         * Initialize the router which can route all request.
         */
        function init_router()
        {
            $this->container['router'] = new AltoRouter();
        }


        function initPlugins()
        {
            $this->container['plugins'] = new MyCMSPlugins($this->container);
        }

        /**
         * Load the security class and check the https.
         */
        function init_security()
        {
            $this->container['security'] = new MyCMSSecurity($this->container['settings']);
            $this->container['security']->my_control_https();
        }

        /**
         * Load the function class, who contain useful functions.
         */
        function init_functions()
        {
            $this->container['functions'] = new MyCMSFunctions();
        }

        /**
         * Initialize the theme class
         */
        function init_theme()
        {
            $this->container['theme'] = new MyCMSTheme($this->container);
            $this->container['themeCustomizer']->setContainer($this->container);
        }

        /**
         * Initialize the users class
         * @throws MyCMSException
         */
        function init_users()
        {
            $users = new MyCMSUsers($this->container);
            //todo finish the functions tag function and enable these functions // make support for the new plugin system
            /*
                        $this->container['theme']->add_functions_tag("{@user_logged_in=start@}", "{@user_logged_in=end@}", "user_logged_in");
                        $this->container['theme']->add_functions_tag("{@user_not_logged_in=start@}", "{@user_not_logged_in=end@}", "user_not_logged_in");
                        $this->container['theme']->add_functions_tag("{@hide_if_logged=start@}", "{@hide_if_logged=end@}", "hide_if_logged");
                        $this->container['theme']->add_functions_tag("{@hide_if_not_logged=start@}", "{@hide_if_not_logged=end@}", "hide_if_not_logged");
            */

            $users->control_ban();
            $users->control_session();
            $users->control_session_admin();
            $users->set_user_tag();

            $this->container['users'] = $users;
        }

        /**
         * Initialize the languages class which can load translation.
         */
        function init_languages()
        {
            $this->container['languages'] = new MyCMSLanguage($this->container);
        }

        /**
         * Set all default theme tag
         */
        function set_tags()
        {
            $this->container['theme']->add_tag('siteNAME', $this->container['settings']->get_settings_value('site_name')); //use {@siteNAME@} in your page
            $this->container['theme']->add_tag('my_cms_version', $this->container['my_cms_version']);
            $this->container['theme']->add_tag('my_php_version', phpversion());
            //todo finish or delete(deprecated)
            $this->container['theme']->add_tag('my_mysql_version', '5.5'); //<-- this
            $this->container['theme']->add_tag('siteURL', $this->container['settings']->get_settings_value('site_url'));
            $this->container['theme']->add_tag('siteTEMPLATE', $this->container['theme']->fix_theme($this->container['settings']->get_settings_value('site_template')));
            $this->container['theme']->add_tag('MY_ADMIN_TEMPLATE_PATH', $this->container['settings']->get_settings_value('site_url') . MY_ADMIN_TEMPLATE_PATH);
            $this->container['theme']->add_tag('MY_PLUGINS_PATH', $this->container['settings']->get_settings_value('site_url') . MY_PLUGINS_PATH);
            $this->container['theme']->add_tag('siteTIMEZONE', $this->container['settings']->get_settings_value('site_timezone'));
            $this->container['theme']->add_tag('siteLANGUAGE', $this->container['settings']->get_settings_value('site_language'));
            $this->container['theme']->add_tag('siteDESCRIPTION', $this->container['settings']->get_settings_value('site_description'));
            $this->container['theme']->add_tag('templateNAME', $this->container['theme']->get_style_info('name'));
            $this->container['theme']->add_tag('templateVERSION', $this->container['theme']->get_style_info('version'));
            $this->container['theme']->add_tag('templateAUTHOR', $this->container['theme']->get_style_info('author'));
            $this->container['theme']->add_tag('templateCMS_VERSION', $this->container['theme']->get_style_info('cms_version'));
        }

        /**
         * Initialize the blog class
         */
        function init_blog()
        {
            $this->container['blog'] = new MyCMSBlog($this->container);
            $this->container['theme']->setContainer($this->container);
        }

        /**
         * Load the theme functions and settings.
         */
        function init_theme_functions()
        {
            $ThemeFunctions = new MyCMSThemeFunctions($this->container['theme']);
            $ThemeFunctions->set_theme_tags();

            $this->container['theme_functions'] = $ThemeFunctions;
        }

        /**
         * Set the admin routes
         */
        function init_admin()
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
        function init_page_loader()
        {
            $page_loader = new MyCMSPageLoader($this->container);
            $page_loader->page_loader_match_database_page();
            $this->container['page_loader'] = $page_loader;
        }

        /**
         * Load the api class
         */
        function init_api()
        {
            $this->container['api'] = new MyCMSApi($this->container);
        }

        /**
         * Initialize the theme class
         */
        function initThemeCustomizer()
        {
            $this->container['themeCustomizer'] = new MyCMSThemeCustomizer($this->container);
        }

        /**
         * Load the page and send response to the user.
         */
        function send_response()
        {
            if (!$this->initialized)
                return;

            if ($this->container['api']->isApi()) {
                $this->container['api']->showApi();
            }

            $this->container['router']->setBasePath(MY_BASE_PATH);
            $match = $this->container['router']->match();

            $this->container['theme']->add_tag('my_cms_welcome_h1', $this->container['languages']->e('my_cms_welcome_h1', true));

            if ($this->is_in_console()) {
                $this->container['theme']->start_console_mode();
            }

            if (defined("LOADER_LOAD_PAGE") && LOADER_LOAD_PAGE == true) {

                if (empty($match['target'])) {
                    $style_info = $this->container['theme']->style_info(MY_THEME);
                    $match['target'] = $style_info["style_error_page"];
                }

                if ($this->container['theme']->is_admin_url($match['target']) == false) {
                    $this->container['theme']->control_maintenance($match['target']);
                    $info = $this->container['page_loader']->load_database_page($match['target']);
                    if ($info == false) {
                        if (!isset($match['params'])) {
                            $match['params'] = [];
                        }
                        $this->container['theme']->load_theme($match['target'], $match['params']);
                    }
                } else {
                    $this->container['theme']->admin_load_theme($match['target'], $match['params']);
                }

            }
        }

        function initPluginsInitializedEvent()
        {
            $this->container['themeCustomizer']->applyCustomizerLateEvents();
            $this->container['plugins']->addEvent('initialized', '');
        }

        function updatePluginContainer()
        {
            $this->container["plugins"]->setContainer($this->container);
        }
    }
