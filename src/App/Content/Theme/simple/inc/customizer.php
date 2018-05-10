<?php

use MyCMS\App\Utils\Management\MyCMSContainer;

class MyCMSSimpleCustomizer extends MyCMSContainer
{
    private $customizer;

    function __construct($container)
    {
        $this->setContainer($container);
        $this->container['plugins']->addEvent('customizerRegister', [$this, 'setCustomizer']);
    }

    function setCustomizer(\MyCMS\App\Utils\Theme\MyCMSThemeCustomizer $customizer)
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
        $customizer->addSubMenuItemSelector("siteDescription", "#siteDescription");

        $customizer->addMenu("theme_colors", $this->container['languages']->e("customizer_theme_colors", true));
        $customizer->addMenu("theme_settings", $this->container['languages']->e("customizer_theme_settings", true));

        /*$customizer->addSimpleSubMenuItem("ciao", "theme_colors_s", "CustomizerSimpleText", [
            "label"       => "Ciao",
            "value"       => "Ciao",
            "description" => "Questa casella server per testare asdasdasd",
            "placeholder" => "test"
        ], function () {
        });*/

        $selectPageOptions = [
            $this->container['languages']->e("customizer_theme_home_page_select_simple_articles", true) => 'articlesSimpleThemePage'
        ];
        $pagesQuery = $this->container['database']->query("SELECT * FROM my_page WHERE pagePublic = '1'");

        foreach ($pagesQuery as $page)
        {
            $selectPageOptions[$this->container['functions']->removeSpace($page['pageTitle'])] = $page['pageIdMenu'];
        }

        if(isset($selectPageOptions['Blog']))
            unset($selectPageOptions['Blog']);

        $customizer->addSimpleSubMenuItem("homePageViewSetting", "theme_settings_s", "CustomizerSimpleSelect",
            [
                "label"       => $this->container['languages']->e("customizer_theme_home_page_select", true),
                "options" => $selectPageOptions
            ],
            function ($value, $container) {
                $container['theme']->setThemeSetting("homePageView", $value);
            }
        );

        $customizer->addSimpleSubMenuItem("resetThemeSettings", "theme_settings_s", "CustomizerSimpleOnClickButton",
            [
                "value" => $this->container['languages']->e("customizer_theme_reset_settings", true),
                "href" => "#"
            ],
            function ()
            {
                
            },
            function ($itemId, $container) {
                return "$('#" . $itemId . "').click(function(){ customizerApi('action=resetThemeSettings&themeName=". MY_THEME ."') });";
            }
        );

        $customizer->addSimpleSubMenuItem("selectHeaderImage", "theme_settings_s", "CustomizerSimpleOnClickButton",
            [
                "value" => $this->container['languages']->e("customizer_theme_select_header_img_settings", true),
                "href" => "/my-admin/upload/mediaAddon",
                "customArgs" => [
                    "data-featherlight" => "iframe"
                ]
            ],
            function ()
            {

            },
            function ($itemId, $container)
            {
                $return = "var tmpMediaAddonUrl = false; var disableMediaType = true; var mediaAddonDataQuery =  {
            mimeType: \"image\",
            search: \"\",
            orderby: \"date\",
            order: \"desc\"
        };";
                return $return . "var tmpMediaAddonFunctionCallBack = function(){ customizerApi('action=changeThemeSettings&key=headerImg&value='+tmpMediaAddonUrl); $('#previewFrame').contents().find('div.headerImage').find('img').attr('src', tmpMediaAddonUrl) };";
            }
        );

        $customizer->addSimpleSubMenuItem("backgroundColor", "theme_colors_s", "CustomizerColorInput",
            [
                "label" => "Background Color",
                "value" => $customizer->getContainer()['theme']->getThemeSetting("backgroundColor")
            ],

            function ($value, $container) {
                $container['theme']->setThemeSetting("backgroundColor", $value);
            },
            function ($itemId) {
                return "$('#" . $itemId . "').change(function(){getElement($('.siteContainer')).css('background-color', $('#" . $itemId . "').val());});";
            }
        );

        $customizer->addSimpleSubMenuItem("textColor", "theme_colors_s", "CustomizerColorInput",
            [
                "label" => "Text Color",
                "value" => $customizer->getContainer()['theme']->getThemeSetting("textColor")
            ],

            function ($value, $container) {
                $container['theme']->setThemeSetting("textColor", $value);
            },
            function ($itemId) {
                return "$('#" . $itemId . "').change(function(){getElement($('.siteContainer')).css('color', $('#" . $itemId . "').val());});";
            }
        );

        $customizer->addSimpleSubMenuItem("siteInfoColor", "theme_colors_s", "CustomizerColorInput",
            [
                "label" => "Website info color",
                "value" => $customizer->getContainer()['theme']->getThemeSetting("siteInfoColor")
            ],

            function ($value, $container) {
                $container['theme']->setThemeSetting("siteInfoColor", $value);
            }
        );
    }
}


$MyCMSSimpleCustomizer = new MyCMSSimpleCustomizer($this->container);

$this->setThemeSetting("backgroundColor", "#ffffff", true);
$this->setThemeSetting("siteInfoColor", "#ffffff", true);
$this->setThemeSetting("textColor", "#525255", true);
$this->setThemeSetting("headerImg", "{@siteURL@}/src/App/Content/Theme/{@siteTEMPLATE@}/assets/img/header.jpg", true);
$this->setThemeSetting("homePageView", "articlesSimpleThemePage", true);

$this->container['theme']->addTag("headerImg", $this->getThemeSetting("headerImg"));
$this->container['theme']->addTag("backgroundColor", $this->getThemeSetting("backgroundColor"));
$this->container['theme']->addTag("siteInfoColor", $this->getThemeSetting("siteInfoColor"));
$this->container['theme']->addTag("textColor", $this->getThemeSetting("textColor"));
$this->container['theme']->addTag("homePageView", $this->getThemeSetting("homePageView"));
