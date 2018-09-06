<?php
/**
 * User: tuttarealstep
 * Date: 23/05/18
 * Time: 20.18
 */

class MyCMSSchema
{
    /**
     * Application container
     * @var
     */
    private $container;

    function __construct($container)
    {
        $this->container = $container;
    }

    function databaseSchema($type = "all")
    {
        $blogTable = "CREATE TABLE my_blog (
  postId int(11) NOT NULL AUTO_INCREMENT,
  postTitle text NOT NULL,
  postContent longtext NOT NULL,
  postDate datetime NOT NULL,
  postAuthor int(11) NOT NULL,
  postName varchar(200) NOT NULL,
  postStatus varchar(20) NOT NULL DEFAULT 'publish',
  postType varchar(20) NOT NULL DEFAULT 'post',
  postModified datetime NOT NULL,
  commentStatus varchar(20) NOT NULL DEFAULT 'open',
  PRIMARY KEY (postId),
  UNIQUE KEY postId (postId)
) DEFAULT CHARSET=utf8;";

        $blogCategoryTable = "CREATE TABLE my_blog_category (
  categoryId int(11) NOT NULL AUTO_INCREMENT,
  categoryName varchar(200) NOT NULL,
  categoryDescription text NOT NULL,
  PRIMARY KEY (categoryId)
) DEFAULT CHARSET=utf8;";

        $blogCategoryRelationship = "CREATE TABLE my_blog_category_relationships (
  postId int(11) NOT NULL,
  categoryId int(11) NOT NULL
) DEFAULT CHARSET=utf8;";

        $blogPostComments = "CREATE TABLE my_blog_post_comments (
  id int(11) NOT NULL AUTO_INCREMENT,
  author int(11) NOT NULL,
  comments varchar(250) NOT NULL,
  postid int(11) NOT NULL,
  date varchar(100) NOT NULL,
  enable enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
) DEFAULT CHARSET=utf8;";

        $settingsTable = "CREATE TABLE my_cms_settings (
  settings_id int(11) NOT NULL AUTO_INCREMENT,
  settings_name varchar(100) NOT NULL,
  settings_value text NOT NULL,
  PRIMARY KEY (settings_id)
) DEFAULT CHARSET=utf8;";

        $languageTable = "CREATE TABLE my_language (
  language_id int(11) NOT NULL AUTO_INCREMENT,
  language_name varchar(100) NOT NULL,
  language_language varchar(50) NOT NULL,
  PRIMARY KEY (language_id)
) DEFAULT CHARSET=utf8;";

        $mediaTable = "CREATE TABLE my_media (
  id int(11) NOT NULL AUTO_INCREMENT,
  title text NOT NULL,
  description text NOT NULL,
  date datetime NOT NULL,
  dateEdit datetime NOT NULL,
  author int(11) NOT NULL,
  name text NOT NULL,
  caption text NOT NULL,
  mime_type varchar(100) NOT NULL,
  PRIMARY KEY (id)
) DEFAULT CHARSET=utf8;";

        $menuTable = "CREATE TABLE my_menu (
  menu_id int(11) NOT NULL AUTO_INCREMENT,
  menu_name varchar(20) NOT NULL,
  menu_page_id varchar(50) NOT NULL,
  menu_link varchar(255) NOT NULL,
  menu_icon enum('fa','glyphicon','false') NOT NULL DEFAULT 'false',
  menu_icon_image varchar(100) NOT NULL,
  menu_dropdown enum('0','1') NOT NULL DEFAULT '0',
  menu_dropdown_parent int(11) NOT NULL DEFAULT '0',
  menu_sort int(11) NOT NULL,
  menu_enabled enum('1','0') NOT NULL DEFAULT '1',
  menu_can_delete enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (menu_id)
) DEFAULT CHARSET=utf8;";

        $pageTable = "CREATE TABLE my_page (
  pageId int(11) NOT NULL AUTO_INCREMENT,
  pageTitle text NOT NULL,
  pageUrl text NOT NULL,
  pagePublic enum('0','1') NOT NULL DEFAULT '1',
  pageIdMenu varchar(200) NOT NULL,
  pageInTheme enum('0','1') NOT NULL DEFAULT '0',
  pageHtml text,
  pageCanDelete enum('0','1') NOT NULL DEFAULT '1',
  pageCustomCss text,
  PRIMARY KEY (pageId)
) DEFAULT CHARSET=utf8;";

        $securityCookieTable = "CREATE TABLE my_security_cookie (
  cookie_id int(11) NOT NULL AUTO_INCREMENT,
  cookie_name varchar(100) NOT NULL,
  cookie_value varchar(300) NOT NULL,
  cookie_user int(11) NOT NULL,
  cookie_expire varchar(100) NOT NULL,
  cookie_agent varchar(200) NOT NULL,
  cookie_ip varchar(100) NOT NULL,
  PRIMARY KEY (cookie_id)
) DEFAULT CHARSET=utf8;";

        $styleTable = "CREATE TABLE my_style (
  style_id int(11) NOT NULL AUTO_INCREMENT,
  style_name varchar(100) NOT NULL,
  style_author varchar(200) NOT NULL,
  style_path_name varchar(200) NOT NULL,
  style_error_page varchar(255) NOT NULL,
  style_maintenance_page varchar(255) NOT NULL,
  style_json_file_url text NOT NULL,
  style_version varchar(200) NOT NULL,
  style_languages text NOT NULL,
  style_enable_remove enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (style_id)
) DEFAULT CHARSET=utf8;";

        $usersTable = "CREATE TABLE my_users (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(100) NOT NULL,
  surname varchar(100) NOT NULL,
  password text NOT NULL,
  mail varchar(100) NOT NULL,
  ip varchar(100) NOT NULL,
  rank varchar(250) NOT NULL DEFAULT 'user',
  last_access varchar(20) NOT NULL DEFAULT '0000-00-00 00:00:00',
  adminColor varchar(100) NOT NULL DEFAULT 'default',
  PRIMARY KEY (id)
) DEFAULT CHARSET=utf8;";

        $usersBannedTable = "CREATE TABLE my_users_banned (
  id int(11) NOT NULL AUTO_INCREMENT,
  user_ip varchar(100) NOT NULL,
  expire_date datetime NOT NULL,
  PRIMARY KEY (id)
) DEFAULT CHARSET=utf8;";


        $blogTables = $blogTable . $blogCategoryTable . $blogCategoryRelationship . $blogPostComments;
        $generalTables = $settingsTable . $languageTable . $mediaTable . $menuTable . $pageTable . $styleTable;
        $usersTable = $securityCookieTable . $usersTable . $usersBannedTable;

        switch ($type) {
            case "blog":
                return $blogTables;
                break;
            case "general":
                return $generalTables;
                break;
            case "all":
            default:
                return $blogTables . $generalTables . $usersTable;
                break;
        }
    }

    function setDatabaseValues()
    {
        $this->container['database']->query("INSERT INTO my_language (language_id, language_name, language_language) VALUES (1, 'Italiano - Italian', 'it_IT'), (2, 'English - English', 'en_US')");
        $this->container['database']->query("INSERT INTO my_menu (menu_id, menu_name, menu_page_id, menu_link, menu_icon, menu_icon_image, menu_dropdown, menu_dropdown_parent, menu_sort, menu_enabled, menu_can_delete) VALUES (1, 'Blog', 'blog', '{@siteURL@}/blog', 'false', '', '0', 0, 2, '1', '0'), (2, 'Home', '', '{@siteURL@}', 'false', '', '0', 0, 0, '1', '1')");
        $this->container['database']->query("INSERT INTO my_page (pageId, pageTitle, pageUrl, pagePublic, pageIdMenu, pageInTheme, pageHtml, pageCanDelete) VALUES (1, 'Blog', '{@siteURL@}/blog', '1', 'blog', '1', NULL, '0')");
        $this->container['database']->query("INSERT INTO my_style (style_id, style_name, style_author, style_path_name, style_error_page, style_maintenance_page, style_json_file_url, style_version, style_languages, style_enable_remove) VALUES (1, 'MyCMS Default', 'MyCMS', 'default', '404', 'maintenance', '', '0.0.0.1', 'it_IT,en_US', '0')");
        $this->container['database']->query("INSERT INTO my_style (style_id, style_name, style_author, style_path_name, style_error_page, style_maintenance_page, style_json_file_url, style_version, style_languages, style_enable_remove) VALUES (2, 'Simple Theme', 'MyCMS', 'simple', 'error', 'maintenance', '', '0.0.0.1', 'it_IT,en_US', '1')");
    }

    function setSettings($siteName = "MyCMS 6", $siteUrl = "http://localhost")
    {
        $settings = [
            "site_name" => $siteName,
            "site_url" => $siteUrl,
            "site_template" => "default",
            "site_timezone" => "Europe/Rome",
            "site_language" => "en_US",
            "blog_post_control_comments" => "0",
            "site_description" => "Welcome in MyCMS 6",
            "site_maintenance" => "false",
            "blog_private" => "false",
            "blog_comments_active" => "true",
            "blog_comments_approve" => "false",
            "site_use_cache" => "false",
            "site_template_language" => "en_US",
            "site_private" => "false",
        ];

        foreach ($settings as $key => $value)
        {
            $this->container['database']->query("INSERT INTO my_cms_settings (settings_id, settings_name, settings_value) VALUES (NULL, :key, :value)", ["key" => $key, "value" => $value]);
        }
    }

    function setRoles()
    {
        $this->container['roles']->addRole("user", "User",  [
            "read" => true
        ]);
        $this->container['roles']->addRole("author", "Author",  [
            "read" => true,
            "edit_posts" => true,
            "delete_posts" => true,
            "publish_posts" => true,
            "upload_files" => true,
            "edit_published_posts" => true,
            "delete_published_posts" => true,
        ]);
        $this->container['roles']->addRole("editor", "Editor",  [
            "read" => true,
            "edit_posts" => true,
            "delete_posts" => true,
            "publish_posts" => true,
            "upload_files" => true,
            "edit_published_posts" => true,
            "delete_published_posts" => true,
            "read_private_posts" => true,
            "read_private_pages" => true,
            "publish_pages" => true,
            "moderate_comments" => true,
            "manage_links" => true,
            "manage_categories" => true,
            "edit_published_pages" => true,
            "edit_private_posts" => true,
            "edit_private_pages" => true,
            "edit_pages" => true,
            "edit_others_posts" => true,
            "edit_others_pages" => true,
            "delete_published_pages" => true,
            "delete_private_pages" => true,
            "delete_private_posts" => true,
            "delete_pages" => true,
            "delete_others_posts" => true
        ]);

        $this->container['roles']->addRole("administrator", "Administrator",  [
            "read" => true,
            "edit_posts" => true,
            "delete_posts" => true,
            "publish_posts" => true,
            "upload_files" => true,
            "edit_published_posts" => true,
            "delete_published_posts" => true,
            "unfiltered_html" => true,
            "read_private_posts" => true,
            "read_private_pages" => true,
            "publish_pages" => true,
            "moderate_comments" => true,
            "manage_links" => true,
            "manage_categories" => true,
            "edit_published_pages" => true,
            "edit_private_posts" => true,
            "edit_private_pages" => true,
            "edit_pages" => true,
            "edit_others_posts" => true,
            "delete_published_pages" => true,
            "delete_private_pages" => true,
            "delete_private_posts" => true,
            "delete_pages" => true,
            "delete_others_posts" => true,
            "create_users" => true,
            "edit_users" => true,
            "edit_files" => true,
            "edit_themes" => true,
            "delete_themes" => true,
            "upload_themes" => true,
            "install_themes" => true,
            "update_themes" => true,
            "update_cms" => true,
            "customize" => true,
            "switch_themes" => true,
            "promote_users" => true,
            "manage_options" => true,
            "list_users" => true,
            "import" => true,
            "export" => true,
            "use_cmd" => true,
            "show_user_menu" => true,
            "manage_plugins" => true,
            "delete_users" => true
        ]);
    }

    function writeConfig($config_host = "", $config_user = "", $config_password = "", $config_database = "")
    {
        $configFile = "<?php
/*
    MyCMS 6
    
    By Stefano Valenzano (Tuttarealstep)
*/

	//HOSTNAME
	define(\"C_HOST\", \"" . $config_host . "\");

	//DATABASE USER
	define(\"C_USER\", \"" . $config_user . "\");

	//DATABASE PASSWORD
	define(\"C_PASSWORD\", \"" . $config_password . "\");

	//DATABASE NAME
	define(\"C_DATABASE\", \"" . $config_database . "\");

//OTHERS
	//MODALITY
	define(\"MY_M_DEBUG\", false);  //If true show all errors

//KEY
	define('SESSION_KEY_GENERATE', true);
	define('SESSION_KEY', 'MYCMS_" . $this->myGenerateRandom(6) . "');
	define('SECRET_KEY', '" . $this->myGenerateRandom(50) . "');
	define('CRYPT_KEY', '" . $this->myGenerateRandom(50) . "');
	
//THEME
	define('ENABLE_TWIG_TEMPLATE_ENGINE', true);
    define('ENABLE_TWIG_TEMPLATE_DEBUG', false);
";
        if (is_writable(dirname(__FILE__) . "/../../Configuration/"))
        {
            $file = @fopen(dirname(__FILE__) . "/../../Configuration/my_config.php", 'w');
            fwrite($file, $configFile);
            return true;
        } else {
            return $configFile;
        }
    }

    function myGenerateRandom($length)
    {
        switch (true) {
            case function_exists("mcrypt_create_iv") :
                $random = mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);
                break;
            case function_exists("openssl_random_pseudo_bytes") :
                $random = openssl_random_pseudo_bytes($length);
                break;
            default :
                $i = 0;
                $random = "";
                while ($i < $length):
                    $i++;
                    $random .= chr(mt_rand(0, 255));
                endwhile;
                break;
        }

        return substr(bin2hex($random), 0, $length);
    }

    function dropDatabaseTables()
    {
        $sql = "DROP TABLE IF EXISTS my_blog;";
        $sql .= "DROP TABLE IF EXISTS my_blog_category;";
        $sql .= "DROP TABLE IF EXISTS my_blog_category_relationships;";
        $sql .= "DROP TABLE IF EXISTS my_blog_post_comments;";
        $sql .= "DROP TABLE IF EXISTS my_cms_settings;";
        $sql .= "DROP TABLE IF EXISTS my_language;";
        $sql .= "DROP TABLE IF EXISTS my_media;";
        $sql .= "DROP TABLE IF EXISTS my_menu;";
        $sql .= "DROP TABLE IF EXISTS my_page;";
        $sql .= "DROP TABLE IF EXISTS my_security_cookie;";
        $sql .= "DROP TABLE IF EXISTS my_style;";
        $sql .= "DROP TABLE IF EXISTS my_users;";
        $sql .= "DROP TABLE IF EXISTS my_users_banned;";
        return $sql;
    }
}