<?php
/**
 * User: tuttarealstep
 * Date: 10/04/16
 * Time: 11.26
 */

namespace MyCMS\App\Utils\Facilities;

class MyCMSThemeFunctions
{
    private $theme;

    function __construct($theme)
    {
        $this->theme = $theme;
    }

    function setThemeTags()
    {
        $this->theme->addTag('getSTYLE=css', $this->theme->setTag($this->theme->getStyleScript("css", true)));
        $this->theme->addTag('getSTYLE=script', $this->theme->setTag($this->theme->getStyleScript("script", true)));
    }
}