<?php

use MyCMS\App\Utils\Management\MyCMSContainer;

class MyCMSDefaultCustomizer extends MyCMSContainer
{
    private $customizer;

    function __construct($container)
    {
        $this->setContainer($container);
        $this->container['plugins']->addEvent('customizerRegister', [$this, 'setCustomizer']);
    }

    function setCustomizer(\MyCMS\App\Utils\Theme\MyCMSThemeCustomizer $customizer = null)
    {
        $this->customizer = $customizer;

        $this->myCMSThemeCustomizer();
    }

    function myCMSThemeCustomizer()
    {
        if ($this->customizer == null)
            return;

        $customizer = $this->customizer;

        $customizer->addSubMenuItemSelector("siteTitle", "#siteName");
        $customizer->addSubMenuItemSelector("siteTitle", ".navbar-brand");
        $customizer->addSubMenuItemSelector("siteDescription", "#siteDescription");

        $customizer->addMenu("theme_colors", $this->container['languages']->e("customizer_theme_colors", true));


        /*$customizer->addSimpleSubMenuItem("ciao", "theme_colors_s", "CustomizerSimpleText", [
            "label"       => "Ciao",
            "value"       => "Ciao",
            "description" => "Questa casella server per testare asdasdasd",
            "placeholder" => "test"
        ], function () {
        });*/

        $customizer->addSimpleSubMenuItem("backgroundColor", "theme_colors_s", "CustomizerColorInput",
            [
                "label" => "Background Color",
                "value" => $customizer->getContainer()['theme']->getThemeSetting("backgroundColor")
            ],

            function ($value, $container) {
                $container['theme']->setThemeSetting("backgroundColor", $value);
            },
            function ($itemId) {
                return "$('#" . $itemId . "').change(function(){getElement('body').css('background-color', $('#" . $itemId . "').val());});";
            }
        );

        $customizer->addSimpleSubMenuItem("navbarBackgroundColor", "theme_colors_s", "CustomizerColorInput",
            [
                "label" => "NavBar Background Color",
                "value" => $customizer->getContainer()['theme']->getThemeSetting("navbarBackgroundColor")
            ],

            function ($value, $container) {
                $container['theme']->setThemeSetting("navbarBackgroundColor", $value);
            },
            function ($itemId) {
                return "$('#" . $itemId . "').change(function(){getElement('.navbar-mycms').css('background-color', $('#" . $itemId . "').val());});";
            }
        );

        $customizer->addSimpleSubMenuItem("navbarLogoColor", "theme_colors_s", "CustomizerColorInput",
            [
                "label" => "NavBar Logo Color",
                "value" => $customizer->getContainer()['theme']->getThemeSetting("navbarLogoColor")
            ],

            function ($value, $container) {
                $container['theme']->setThemeSetting("navbarLogoColor", $value);
            },
            function ($itemId) {
                return "$('#" . $itemId . "').change(function(){getElement('.navbar-brand').css('color', $('#" . $itemId . "').val());});";
            }
        );

        $customizer->addSimpleSubMenuItem("navbarMenuTextColor", "theme_colors_s", "CustomizerColorInput",
            [
                "label" => "NavBar Menu Text Color",
                "value" => $customizer->getContainer()['theme']->getThemeSetting("navbarMenuTextColor")
            ],

            function ($value, $container) {
                $container['theme']->setThemeSetting("navbarMenuTextColor", $value);
            },
            function ($itemId) {
                return "$('#" . $itemId . "').change(function(){getElement('.navbar-mycms .nav li > a').css('color', $('#" . $itemId . "').val());});";
            }
        );

        $customizer->addSimpleSubMenuItem("bodyColor", "theme_colors_s", "CustomizerColorInput",
            [
                "label" => "Text Color",
                "value" => $customizer->getContainer()['theme']->getThemeSetting("bodyColor")
            ],

            function ($value, $container) {
                $container['theme']->setThemeSetting("bodyColor", $value);
            },
            function ($itemId) {
                return "$('#" . $itemId . "').change(function(){getElement('body').css('color', $('#" . $itemId . "').val());});";
            }
        );
    }
}


$MyCMSDefaultCustomizer = new MyCMSDefaultCustomizer($this->container);

$this->setThemeSetting("backgroundColor", "#FAFAFA", true);
$this->setThemeSetting("navbarBackgroundColor", "#FFFFFF", true);
$this->setThemeSetting("navbarLogoColor", "#2F4550", true);
$this->setThemeSetting("navbarMenuTextColor", "#2F4550", true);
$this->setThemeSetting("bodyColor", "#525255", true);


$this->container['theme']->addTag("backgroundColor", $this->getThemeSetting("backgroundColor"));
$this->container['theme']->addTag("navbarBackgroundColor", $this->getThemeSetting("navbarBackgroundColor"));
$this->container['theme']->addTag("navbarLogoColor", $this->getThemeSetting("navbarLogoColor"));
$this->container['theme']->addTag("navbarMenuTextColor", $this->getThemeSetting("navbarMenuTextColor"));
$this->container['theme']->addTag("bodyColor", $this->getThemeSetting("bodyColor"));
