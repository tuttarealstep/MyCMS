<?php

class checkMyCMSPermissions
{
    public $container = [];

    function __construct($container)
    {
        $this->container = $container;
        $this->addEvents();
    }

    private function addEvents()
    {
        $this->container['theme']->addConsoleCommand("checkPermissions", [$this, "checkPermissions"], false, "Check MyCMS permissions ");
        $this->checkUXPermissions();
    }

    public function checkUXPermissions()
    {
        if (!$this->validPermissions(MY_ADMIN_PATH . "/Pages/update.php", 777))
            $this->container['plugins']->addEvent("updateBody", function () {
                echo "<div class=\"alert alert-danger\" style='border-radius: 0;'><b>[Check Permissions Plugin] This file has wrong permissions!</b></div>";
            });
    }

    private function validPermissions($path, $perm = 755)
    {
        $code = (int)$this->container['security']->getFilePermission($path);
        if ($code >= $perm) {
            return true;
        }

        return false;
    }

    public function checkPermissions()
    {
        print("MyCMS check permissions - Console Plugin\n\n");

        $this->check(MY_ADMIN_PATH . "/Pages/update.php", 777);
        $this->check(MY_ADMIN_PATH . "/Pages/code_editor.php");
        $this->check(MY_ADMIN_PATH . "/Pages/theme_manager.php");
        $this->check(C_PATH . "/Ajax/code_editor_file_view.php");
        $this->check(C_PATH . "/Ajax/code_editor_new_file.php", 777);
        $this->check(C_PATH . "/Ajax/code_editor_save_file.php", 777);
        $this->check(C_PATH . "/Ajax/code_editor_tree_view.php");
        $this->check(C_PATH . "/Storage/", 777);
        $this->check(I_PATH . "/Security/MyCMSSecurity.php");
        $this->check(I_PATH . "/Theme/MyCMSTheme.php");
        $this->check(I_PATH . "/Facilities/MyCMSFunctions.php", 777);
        $this->check(I_PATH . "/Management/MyCMSCache.php", 777);
        $this->check(I_PATH . "/Management/MyCMSFileManager.php", 777);
        $this->check(I_PATH . "/PageLoader/MyCMSPageLoader.php");
        $this->check(I_PATH . "/Plugins/MyCMSPlugins.php");
    }

    private function check($path, $perm = 755)
    {
        print("Check " . basename($path) . ": " . $path . " ");
        $code = (int)$this->container['security']->getFilePermission($path);
        if ($code >= $perm) {
            echo "- OK (" . $code . ")";
        } else {
            echo "- WRONG PERMISSIONS (" . $code . ", need " . $perm . ")";
        }

        echo "\n";
    }
}

new checkMyCMSPermissions($this->container);