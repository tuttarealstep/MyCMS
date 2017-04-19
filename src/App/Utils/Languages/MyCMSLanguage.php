<?php
/**
 * User: tuttarealstep
 * Date: 10/04/16
 * Time: 0.25
 */

namespace MyCMS\App\Utils\Languages;

use MyCMS\App\Utils\Exceptions\MyCMSException;
use MyCMS\App\Utils\Theme\MyCMSTheme;

/**
 * Class MyCMSLanguage
 * @package MyCMS\App\Utils\Languages
 *
 * Class for manage admin and theme languages
 */
class MyCMSLanguage
{
    /**
     * This variable contain the MyCMSTheme class
     * @var MyCMSTheme
     */
    private $theme;

    function __construct($container)
    {
        $settings = $container['settings'];
        $this->theme = new MyCMSTheme($container);

        //LANGUAGE FILE
        if (!defined("MY_LANGUAGE")) {
            $site_language = $settings->getSettingsValue('site_language');
            if (!empty($site_language)) {
                define('MY_LANGUAGE', $site_language);
            } else {
                define('MY_LANGUAGE', 'it_IT');
            }
        }

        if (!defined("MY_LANGUAGE_THEME")) {
            $site_language = $settings->getSettingsValue('site_template_language');
            if (!empty($site_language)) {
                define('MY_LANGUAGE_THEME', $site_language);
            } else {
                define('MY_LANGUAGE_THEME', 'en_US');
            }
        }
    }

    /**
     * Return const MY_LANGUAGE_THEME defined in the constructor
     * this const contain the current enabled theme language
     * @return string
     */
    function getLanguage()
    {
        return MY_LANGUAGE_THEME;
    }

    /**
     * Return the current admin language
     * @return string
     */
    function getLanguageAdmin()
    {
        return MY_LANGUAGE;
    }

    //Translate use e('text'); use for template page NO ADMIN
    /**
     * This function is used for translate strings using a theme language file
     *
     * @param $string
     * @param bool $display
     * @return mixed
     * @throws MyCMSException
     */
    function e($string, $display = false)
    {
        $theme_path = $this->theme->getThemePath();
        if (!file_exists($theme_path)) {
            throw new MyCMSException("Theme not found!");
        }

        $file_language_name = '' . MY_LANGUAGE_THEME;
        $path = $theme_path . '/languages/';

        @include($path . $file_language_name . '.php');

        if (!empty($language[ $string ])) {
            if ($display == true) {
                return $language[ $string ]; //Ritorno come dato
            } else {
                echo $language[ $string ]; //Ritorno per testo
            }
        } else {
            if ($display == true) {
                return $string;
            } else {
                echo $string;
            }
        }

    }

    //Deprecated
    /**
     * This function will be removed soon
     * use "ta" function for translate in the admin panel
     *
     * @param $string
     * @param string $display
     * @return mixed
     */
    function ea($string, $display = '0')
    {
        $file_language_name = 'admin_' . MY_LANGUAGE;
        $path = MY_ADMIN_PATH . '/languages/';
        @include($path . $file_language_name . '.php');

        if (!empty($language[ $string ])) {
            if ($display == '1') {
                return $language[ $string ]; //Ritorno come dato
            } else {
                echo $language[ $string ]; //Ritorno per testo
            }

        } else {
            if ($display == '1') {
                return $string;
            } else {
                echo $string;
            }
        }
    }

    /**
     * ta = translate admin
     * this is a new function who use a boolean variable instead of a string variable
     *
     * if display == true this return the translated string
     * if display == false this print directly the string with a echo functions.
     *
     * @param $string
     * @param bool $display
     * @return string
     */
    function ta($string, $display = false)
    {
        $file_language_name = 'admin_' . MY_LANGUAGE;
        $path = MY_ADMIN_PATH . '/languages/';
        @include($path . $file_language_name . '.php');

        if (!empty($language[ $string ])) {
            if ($display) {
                return $language[ $string ]; //Ritorno come dato
            } else {
                echo $language[ $string ]; //Ritorno per testo
            }

        } else {
            if ($display) {
                return $string;
            } else {
                echo $string;
            }
        }

        return "";
    }
}