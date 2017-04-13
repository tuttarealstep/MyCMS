<?php
    /**
     * User: tuttarealstep
     * Date: 10/04/16
     * Time: 13.14
     *
     * Support for old MyCMS themes global functions.
     */

    /* Set globals */

    $GLOBALS['my_blog'] = $App->container['blog'];
    $GLOBALS['my_db'] = $App->container['database'];
    $GLOBALS['my_users'] = $App->container['users'];
    $GLOBALS['my_language'] = $App->container['languages'];
    $GLOBALS['my_theme'] = $App->container['theme'];
    $GLOBALS['my_settings'] = $App->container['settings'];
    $GLOBALS['my_security'] = $App->container['security'];
    $GLOBALS['my_functions'] = $App->container['functions'];

    function e($string, $display = '0')
    {
        global $my_language;

        return $my_language->e($string, $display);
    }

    function ea($string, $display = '0')
    {
        global $my_language;

        return $my_language->ea($string, $display);
    }

    function set_TAG($page)
    {

        global $my_theme;

        return $my_theme->set_TAG($page);

    }

    function add_tag($tag, $value)
    {

        global $my_theme;
        $my_theme->add_tag($tag, $value);

    }

//No robots
    function no_robots()
    {

        echo "<meta name='robots' content='noindex,follow' />\n";

    }

    function get_theme_path()
    {
        global $my_theme;
        $path = $my_theme->get_theme_path();

        return $path;
    }

    function add_meta_tag($page_name, $tag)
    {

        global $my_theme;
        $my_theme->add_meta_tag($page_name, $tag);

    }

    function get_meta_tag($page_name)
    {

        global $my_theme;
        $my_theme->get_meta_tag($page_name);

    }

    function add_style_script($type, $link)
    {

        global $my_theme;
        $my_theme->add_style_script($type, $link);

    }

    function get_style_script($type, $return = false)
    {

        global $my_theme;
        if ($return == false) {
            $my_theme->get_style_script($type, $return);
        } else {
            return $my_theme->get_style_script($type, $return);
        }

        return null;
    }

    function add_style_script_admin($type, $link)
    {

        global $my_theme;
        $my_theme->add_style_script_admin($type, $link);

    }

    function get_style_script_admin($type, $return = false)
    {

        global $my_theme;
        if ($return == false) {
            $my_theme->get_style_script_admin($type, $return);
        } else {
            return $my_theme->get_style_script_admin($type, $return);
        }

        return null;
    }

    function fix_theme($theme)
    {

        $theme_path = get_theme_path();

        if (file_exists($theme_path)) {
            return $theme;
        } else {
            return "default";
        }

    }

    function add_functions_tag($start, $end, $function, $param = null)
    {

        global $my_theme;
        $my_theme->add_functions_tag($start, $end, $function, $param);

    }

    function require_page($bool, $page)
    {
        if ($bool == true) {
            if ($page == PAGE_ID) {

            } else {
                header('Location: ' . HOST . '');
                exit;
            }
        }
    }

    function get_file($page, $name = null, $page_loader = false)
    {

        global $my_theme;

        if (empty($name)) {
            $name = '';
        }

        $my_theme->get_file($page, $name, $page_loader);

    }

    function get_page($page, $name = null)
    {

        global $my_theme;

        if (empty($name)) {
            $name = '';
        }

        $my_theme->get_page($page, $name);

    }

    function get_file_admin($page, $name = null, $page_loader = false)
    {

        global $my_theme;

        if (empty($name)) {
            $name = '';
        }

        $my_theme->get_file_admin($page, $name, $page_loader);

    }

    function get_page_admin($page, $name = null)
    {

        global $my_theme;

        if (empty($name)) {
            $name = '';
        }

        $my_theme->get_page_admin($page, $name);

    }

    function get_settings_id($name)
    {

        global $my_settings;

        return $my_settings->get_settings_id($name);

    }

    function get_settings_name($id)
    {

        global $my_settings;

        return $my_settings->get_settings_name($id);
    }

    function get_settings_value($setting_name = "")
    {
        global $my_settings;

        return $my_settings->get_settings_value($setting_name);
    }

    function user_logged_in()
    {

        if (isset($_SESSION['user']['id'])):
            return true;
        else:
            return false;
        endif;

    }

    function user_not_logged_in()
    {

        if (isset($_SESSION['user']['id'])):
            return false;
        else:
            return true;
        endif;

    }

    function staff_logged_in()
    {

        if (isset($_SESSION['staff']['id'])):

            return true;

        else:

            return false;

        endif;

    }

    function hide_if_logged()
    {
        if (user_logged_in()) {
            header("location: " . HOST . "");
            exit;
        }

    }

    function hide_if_not_logged()
    {
        if (!user_logged_in()) {
            header("location: " . HOST . "");
            exit;
        }

    }

    function hide_if_staff_logged()
    {
        if (staff_logged_in()) {
            header("location: " . HOST . "");
            exit;
        }

    }

    function hide_if_staff_not_logged()
    {
        if (!staff_logged_in()) {
            header("location: " . HOST . "");
            exit;
        }

    }

    function isStaff()
    {
        global $my_users;
        if (user_logged_in()) {
            if ($my_users->getInfo($_SESSION['user']['id'], 'rank') >= 2) {
                return true;
            } else {
                return false;
            }

        } else {
            return false;
        }
    }

    function isAdmin()
    {
        global $my_users;
        if (staff_logged_in()) {
            $user_rank = $my_users->getInfo($_SESSION['staff']['id'], 'rank');
            if ($user_rank >= 3) {
                return true;
            } else {
                return false;
            }
        }

        return false;
    }

    function my_sql_secure($string)
    {
        global $my_security;

        return $my_security->my_sql_secure($string);
    }

    function my_generate_random($length)
    {
        global $my_security;

        return $my_security->my_generate_random($length);
    }

    function crypt_md5($value, $time = 1)
    {
        global $my_security;

        return $my_security->crypt_md5($value, $time);
    }

    function my_hash($value)
    {
        global $my_security;

        return $my_security->my_generate_random($value);
    }

    function my_control_https()
    {
        global $my_security;

        return $my_security->my_control_https();
    }

    function my_cms_xml_command($command)
    {
        global $my_security;

        return $my_security->my_cms_xml_command($command);
    }

//THESE FUNCTION WORK ONLY WITH PHP 5.6
    function my_cms_calculate_cost()
    {
        global $my_security;

        return $my_security->my_cms_calculate_cost();
    }


    function my_cms_security_create_password($password)
    {
        global $my_security;

        return $my_security->my_cms_security_create_password($password);
    }


    function s_crypt($str)
    {
        global $my_security;

        return $my_security->s_crypt($str);
    }

    function s_decrypt($str)
    {
        global $my_security;

        return $my_security->s_decrypt($str);
    }

    function remove_space($string)
    {
        global $my_functions;

        return $my_functions->remove_space($string);
    }

    function add_space($string)
    {
        global $my_functions;

        return $my_functions->add_space($string);
    }

    function time_normal_full($string)
    {
        global $my_functions;

        return $my_functions->time_normal_full($string);
    }

    function time_normal_his($string)
    {
        global $my_functions;

        return $my_functions->time_normal_his($string);
    }

    function time_normal($string)
    {
        global $my_functions;

        return $my_functions->time_normal($string);
    }

    function fix_text($string)
    {
        global $my_functions;

        return $my_functions->fix_text($string);
    }

    function remove_dir($dir)
    {
        global $my_functions;

        return $my_functions->remove_dir($dir);
    }