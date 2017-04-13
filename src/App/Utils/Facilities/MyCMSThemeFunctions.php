<?php
    /**
     * User: tuttarealstep
     * Date: 10/04/16
     * Time: 11.26
     */

    namespace MyCMS\App\Utils\Facilities;

    if (!defined("MY_CMS_PATH")) {
        die("NO SCRIPT");
    }

    class MyCMSThemeFunctions
    {
        private $theme;

        function __construct($theme)
        {
            $this->theme = $theme;
        }

        function set_theme_tags()
        {
            $this->theme->add_tag('getSTYLE=css', $this->theme->set_TAG($this->theme->get_style_script("css", true)));
            $this->theme->add_tag('getSTYLE=script', $this->theme->set_TAG($this->theme->get_style_script("script", true)));
        }
    }