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

function setTag($page)
{

    global $my_theme;

    return $my_theme->setTag($page);

}

function addTag($tag, $value)
{

    global $my_theme;
    $my_theme->addTag($tag, $value);

}

//No robots
function noRobots()
{

    echo "<meta name='robots' content='noindex,follow' />\n";

}

function getThemePath()
{
    global $my_theme;
    $path = $my_theme->getThemePath();

    return $path;
}

function addMetaTag($page_name, $tag)
{

    global $my_theme;
    $my_theme->addMetaTag($page_name, $tag);

}

function getMetaTag($page_name)
{

    global $my_theme;
    $my_theme->getMetaTag($page_name);

}

function addStyleScript($type, $link)
{

    global $my_theme;
    $my_theme->addStyleScript($type, $link);

}

function getStyleScript($type, $return = false)
{

    global $my_theme;
    if ($return == false) {
        $my_theme->getStyleScript($type, $return);
    } else {
        return $my_theme->getStyleScript($type, $return);
    }

    return null;
}

function addStyleScriptAdmin($type, $link)
{

    global $my_theme;
    $my_theme->addStyleScriptAdmin($type, $link);

}

function getStyleScriptAdmin($type, $return = false)
{

    global $my_theme;
    if ($return == false) {
        $my_theme->getStyleScriptAdmin($type, $return);
    } else {
        return $my_theme->getStyleScriptAdmin($type, $return);
    }

    return null;
}

function fixTheme($theme)
{

    $theme_path = getThemePath();

    if (file_exists($theme_path)) {
        return $theme;
    } else {
        return "default";
    }

}

function addFunctionsTag($start, $end, $function, $param = null)
{

    global $my_theme;
    $my_theme->addFunctionsTag($start, $end, $function, $param);

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

function getFile($page, $name = null, $page_loader = false)
{

    global $my_theme;

    if (empty($name)) {
        $name = '';
    }

    $my_theme->getFile($page, $name, $page_loader);

}

function getPage($page, $name = null)
{

    global $my_theme;

    if (empty($name)) {
        $name = '';
    }

    $my_theme->getPage($page, $name);

}

function getFileAdmin($page, $name = null, $page_loader = false)
{

    global $my_theme;

    if (empty($name)) {
        $name = '';
    }

    $my_theme->getFileAdmin($page, $name, $page_loader);

}

function getPageAdmin($page, $name = null)
{

    global $my_theme;

    if (empty($name)) {
        $name = '';
    }

    $my_theme->getPageAdmin($page, $name);

}

function getSettingsId($name)
{

    global $my_settings;

    return $my_settings->getSettingsId($name);

}

function getSettingsName($id)
{

    global $my_settings;

    return $my_settings->getSettingsName($id);
}

function getSettingsValue($setting_name = "")
{
    global $my_settings;

    return $my_settings->getSettingsValue($setting_name);
}

function userLoggedIn()
{

    if (isset($_SESSION['user']['id'])):
        return true;
    else:
        return false;
    endif;

}

function userNotLoggedIn()
{

    if (isset($_SESSION['user']['id'])):
        return false;
    else:
        return true;
    endif;

}

function staffLoggedIn()
{

    if (isset($_SESSION['staff']['id'])):

        return true;

    else:

        return false;

    endif;

}

function hideIfLogged()
{
    if (userLoggedIn()) {
        header("location: " . HOST . "");
        exit;
    }

}

function hideIfNotLogged()
{
    if (!userLoggedIn()) {
        header("location: " . HOST . "");
        exit;
    }

}

function hideIfStaffLogged()
{
    if (staffLoggedIn()) {
        header("location: " . HOST . "");
        exit;
    }

}

function hideIfStaffNotLogged()
{
    if (!staffLoggedIn()) {
        header("location: " . HOST . "");
        exit;
    }

}

function isStaff()
{
    global $my_users;
    if (userLoggedIn()) {
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
    if (staffLoggedIn()) {
        $user_rank = $my_users->getInfo($_SESSION['staff']['id'], 'rank');
        if ($user_rank >= 3) {
            return true;
        } else {
            return false;
        }
    }

    return false;
}

function mySqlSecure($string)
{
    global $my_security;

    return $my_security->mySqlSecure($string);
}

function myGenerateRandom($length)
{
    global $my_security;

    return $my_security->myGenerateRandom($length);
}

function cryptMd5($value, $time = 1)
{
    global $my_security;

    return $my_security->cryptMd5($value, $time);
}

function myHash($value)
{
    global $my_security;

    return $my_security->myGenerateRandom($value);
}

function myControlHttps()
{
    global $my_security;

    return $my_security->myControlHttps();
}

function myCmsXmlCommand($command)
{
    global $my_security;

    return $my_security->myCmsXmlCommand($command);
}

//THESE FUNCTION WORK ONLY WITH PHP 5.6
function myCmsCalculateCost()
{
    global $my_security;

    return $my_security->myCmsCalculateCost();
}


function myCmsSecurityCreatePassword($password)
{
    global $my_security;

    return $my_security->myCmsSecurityCreatePassword($password);
}


function s_crypt($str)
{
    return base64_encode($str);
}

function s_decrypt($str)
{
    return base64_decode($str);
}

function removeSpace($string)
{
    global $my_functions;

    return $my_functions->removeSpace($string);
}

function addSpace($string)
{
    global $my_functions;

    return $my_functions->addSpace($string);
}

function timeNormalFull($string)
{
    global $my_functions;

    return $my_functions->timeNormalFull($string);
}

function timeNormalHis($string)
{
    global $my_functions;

    return $my_functions->timeNormalHis($string);
}

function timeNormal($string)
{
    global $my_functions;

    return $my_functions->timeNormal($string);
}

function fixText($string)
{
    global $my_functions;

    return $my_functions->fixText($string);
}

function removeDir($dir)
{
    global $my_functions;

    return $my_functions->removeDir($dir);
}