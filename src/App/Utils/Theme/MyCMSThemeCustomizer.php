<?php
    /**
     * User: tuttarealstep
     * Date: 20/01/17
     * Time: 11.36
     */

    namespace MyCMS\App\Utils\Theme;

    use MyCMS\App\Utils\Management\MyCMSContainer;

    if (!defined("MY_CMS_PATH")) {
        die("NO SCRIPT");
    }

    class MyCMSThemeCustomizer extends MyCMSContainer
    {
        private $menuList = [];
        private $menuListSubMenu = [];
        private $menuSubMenuItems = [];

        public $saveCallBackVector = [];
        public $customJsFunctions = [];

        function __construct($container)
        {
            $this->setContainer($container);
            $this->applyEvents();
            $this->defaultsMenu();
        }

        public function applyCustomizerLateEvents()
        {
            $this->container['plugins']->applyEvent('customizerRegister', $this);
        }

        public function applyEvents()
        {
            $this->container['plugins']->addEvent('customizerAddMenu', [$this, 'addMenu'], 1, 2);
            $this->container['plugins']->addEvent('customizerAddSubMenu', [$this, 'addSubMenu'], 1, 2);
            $this->container['plugins']->addEvent('customizerCustomMenu', [$this, 'getMenu']);
            $this->container['plugins']->addEvent('customizerAddSubMenuItem', [$this, 'addSubMenuItem'], 1, 3);
            $this->container['plugins']->addEvent('customizerAddSubMenuItemCallBack', [$this, 'addSubMenuItemCallBack'], 1, 3);

            $this->container['plugins']->addEvent('customizerJsMenu', [$this, 'customizerJsMenu']);
        }

        private function websiteInfoMenu()
        {
            $this->addMenu("website_info", $this->container['languages']->ta("page_admin_theme_customize_site_info_menu", true));

            $this->addSimpleSubMenuItem("siteTitle", "website_info_s", "CustomizerSimpleText", [
                "label" => $this->container['languages']->ta("page_admin_theme_customize_site_info_menu_title", true),
                "value" => "{@siteNAME@}"
            ], function ($value) {
                if (!empty($value)) {
                    $this->container['settings']->save_settings('site_name', htmlentities($value));
                }
            });

            $this->addSimpleSubMenuItem("siteDescription", "website_info_s", "CustomizerSimpleText", [
                "label" => $this->container['languages']->ta("page_admin_theme_customize_site_info_menu_description", true),
                "value" => "{@siteDESCRIPTION@}"
            ], function ($value) {
                if (!empty($value)) {
                    $this->container['settings']->save_settings('site_description', htmlentities($value));
                }
            });


            $this->addMenu("mypage_editor", $this->container['languages']->ta("page_admin_theme_customize_page_editor_menu", true));

            $this->addSimpleSubMenuItem("myPageId", "mypage_editor_s", "CustomizerSimpleHiddenInput", [
                "value" => ""
            ], function () {
            });

            $this->addSimpleSubMenuItem("myPageSaveButton", "mypage_editor_s", "CustomizerSimpleOnClickButton", [
                "value"      => $this->container['languages']->ta("page_admin_theme_customize_page_editor_menu_save_page", true),
                "jsFunction" => "saveMyPage();"
            ], function () {
            });
        }

        public function defaultsMenu()
        {
            $this->websiteInfoMenu();
        }

        public function addMenu($menuId, $menuName)
        {
            if (!isset($this->menuList[ $menuId ])) {
                $this->menuList[ $menuId ] = ['title' => $menuName];
                $this->addSubMenu($menuId . "_s", $menuId);
            }
        }

        private function addSubMenu($subMenuId, $menuId)
        {
            if (!isset($this->menuList[ $subMenuId ])) {
                $this->menuListSubMenu[ $subMenuId ] = ['menuId' => $menuId];
            }
        }

        public function addSubMenuItem($itemId, $menuId, $content)
        {
            if (!isset($this->menuSubMenuItems[ $itemId ])) {
                $this->menuSubMenuItems[ $itemId ] = ['subMenuId' => $menuId, 'content' => $content];
            }
        }

        public function addSubMenuItemCallBack($itemId, $menuId, $callback)
        {
            if (!isset($this->menuSubMenuItems[ $itemId ])) {
                $this->menuSubMenuItems[ $itemId ] = ['subMenuId' => $menuId, 'content' => "", "callBack" => $callback];
            }
        }

        private function generateRandomId()
        {
            return $this->container['security']->my_generate_random(6);
        }


        /*
         * customJsFunctions($itemid) :
         *
         * function($itemid)
         * {
         *  return "$('".$itemid."').click();";
         * }
         */
        public function addSimpleSubMenuItem($itemId, $subItemId, $className, $values, $saveCallBack, $customJsFunction = null, $customPath = "", $generateRandomId = false)
        {
            if ($generateRandomId) {
                $itemId = $this->generateRandomId();

                while (isset($this->menuSubMenuItems[ $itemId ])) {
                    $itemId = $this->generateRandomId();
                }
            } else {
                if (isset($this->menuSubMenuItems[ $itemId ])) {
                    return false;
                }
            }


            $values = [$itemId, $subItemId, $values];

            $this->saveCallBackVector[ $itemId ] = $saveCallBack;

            $findPath = (!empty($customPath)) ? $customPath : "MyCMS\\App\\Utils\\Theme\\Customizer\\";

            if ($customJsFunction != null && is_callable($customJsFunction)) {
                $this->customJsFunctions[ $itemId ] = $customJsFunction;
            }

            if (file_exists(I_PATH . "/" . "Theme" . "/Customizer/" . $className . ".php")) {
                $className = str_replace('/', '\\', $findPath . $className);
                $customizerClass = new $className(...$values);
                $this->menuSubMenuItems[ $itemId ] = $customizerClass->getSubMenuValues();
            }

        }

        public function addSubMenuItemSelector($itemId, $selector)
        {
            if (isset($this->menuSubMenuItems[ $itemId ])) {
                $this->menuSubMenuItems[ $itemId ]["selector"][] = $selector;
            }
        }

        public function getMenu()
        {
            print("<ul class='ulCustomMenu'>");
            foreach ($this->menuList as $menuId => $value) {
                print ("<li id='li_$menuId'>");
                echo "<div id='$menuId'>";
                echo $value['title'];
                echo '<i class="fa fa-angle-right" aria-hidden="true"></i>';
                echo "</div>";
                if (isset($this->menuListSubMenu[ $menuId . "_s" ])) {
                    print("<ul class='ulCustomMenuSub hidden' id='" . $menuId . '_s' . "'>");
                    ?>
                    <li>
                        <div class="info_div" style="margin-bottom: 10px;">
                            <a href="#" style="color: #fff; font-size: 14px" id="goBack_<?php echo $menuId; ?>"><i
                                        class="fa fa-angle-left"
                                        aria-hidden="true"></i> <?php $this->container['languages']->ea('page_admin_theme_customize_go_back'); ?>
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="info_div" style="margin-bottom: 10px; line-height: 1.8;">
                                    <span class="small_text">
                                        <?php $this->container['languages']->ea('page_admin_theme_customize_customizing'); ?>
                                        <br>
                                        <strong><?php echo $value['title']; ?></strong>
                                    </span>
                        </div>
                    </li>
                    <?php
                    foreach ($this->menuSubMenuItems as $item) {
                        ?>
                        <li>
                            <?php
                                if (isset($item["subMenuId"]) && ($item["subMenuId"] == $menuId . "_s")) {
                                    if (!empty($item['content'])) {
                                        echo $item['content'];
                                    } else {
                                        if (isset($item['callBack']) && is_callable($item['callBack'])) {
                                            call_user_func($item['callBack']);
                                        }
                                    }
                                }
                            ?>
                        </li>
                        <?php
                    }
                    ?>
                    <?php
                    print("</ul>");
                }

                print ("</li>");
            }
            print("</ul>");
        }

        public function customizerJsMenu()
        {
            echo "<script>";
            ?>
            function closeAllMenu()
            {
            <?php
            foreach ($this->menuList as $menuId => $value) {
                if (isset($this->menuListSubMenu[ $menuId . "_s" ])) {
                    ?>
                    if(!$("#<?php echo $menuId; ?>_s").hasClass("hidden"))
                    {
                    $("#goBack_<?php echo $menuId; ?>").click();
                    }
                    <?php
                }
            }
            ?>
            }

            function closeAllMenuEx(subMenuID)
            {
            <?php
            foreach ($this->menuList as $menuId => $value) {
                if (isset($this->menuListSubMenu[ $menuId . "_s" ])) {
                    ?>
                    if(!$("#<?php echo $menuId; ?>_s").hasClass("hidden") && subMenuID != "<?php echo $menuId; ?>_s")
                    {
                    $("#goBack_<?php echo $menuId; ?>").click();
                    }
                    <?php
                }
            }
            ?>
            }

            $("#li_mypage_editor").toggleClass("hidden");

            function openSubMenu(itemId, subMenuID)
            {
            closeAllMenuEx(subMenuID);

            if($("#" + itemId).is(":focus"))
            {
            return;
            }


            $("#" + subMenuID).toggleClass("hidden");


            $("#" + itemId).select();
            }
            <?php
            foreach ($this->menuList as $menuId => $value) {
                if (isset($this->menuListSubMenu[ $menuId . "_s" ])) {
                    ?>
                    $("#<?php echo $menuId; ?>").click(function()
                    {
                    $("#<?php echo $menuId . "_s"; ?>").toggleClass("hidden");
                    });

                    $("#goBack_<?php echo $menuId; ?>").click(function()
                    {
                    $("#<?php echo $menuId . "_s"; ?>").toggleClass("hidden");
                    });

                    <?php
                }
            }
            ?>
            $(function(){


            $("#previewFrame").on('load', function()
            {
            var iframe = $(this).contents();

            /*        $("#previewFrame").find(".navbar-brand").click(function(e)
            {
            alert("sadasd");
            });*/

            <?php

            foreach ($this->menuSubMenuItems as $key => $subValue) {
                if (isset($subValue["selector"])) {
                    foreach ($subValue["selector"] as $sKey => $sVal) {
                        ?>
                        iframe.find('<?php echo $sVal; ?>').mouseover(function()
                        {
                        $(this).css("border", "1px solid #bdbdbd");
                        }).mouseleave(function()
                        {
                        $(this).css("border", "0");
                        });

                        iframe.find('<?php echo $sVal; ?>').click(function(e)
                        {
                        if(e.shiftKey) {
                        e.preventDefault();

                        function defaultAction()
                        {
                        $("#<?php echo $key; ?>").select();
                        openSubMenu("<?php echo $key; ?>","<?php echo $subValue["subMenuId"]; ?>");
                        }


                        defaultAction();

                        //$("#<?php echo $key; ?>").select();
                        // openSubMenu("<?php echo $key; ?>","<?php echo $subValue["subMenuId"]; ?>");
                        }
                        });
                        <?php
                    };
                }
            }
            ?>
            });

            });

            function saveCustomizer()
            {

            if(editMyPage)
            {
            var confirm_v = confirm("<?php $this->container['languages']->ea('page_admin_theme_customize_confirm_to_save'); ?>");
            if(confirm_v == true)
            {
            saveMyPage();
            }
            }


            $.ajax({
            type: 'GET',
            data: {
            <?php
            foreach ($this->menuSubMenuItems as $key => $value) {
                echo $key . ": Base64.encode($('#" . $key . "').val()),\n";
            }
            ?>
            },
            url: myBasePath + "/src/App/Content/Ajax/saveCustomizer.php",
            success: reloadCustomizer,
            error: function(xhr, textStatus, errorThrown) {
            console.log('error');
            }
            });
            }

            <?php
            foreach ($this->customJsFunctions as $key => $function) {
                echo $function($key);
            }
            ?>
            <?php
            echo "</script>";
        }
    }