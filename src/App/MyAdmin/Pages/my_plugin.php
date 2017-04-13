<?php
    hide_if_staff_not_logged();

    $pluginName = $_GET['pluginName'];

    if (!isset($_GET['pluginName'])) {
        header('Location: ' . HOST . '/my-admin/home');
        exit();
    }

    define('PAGE_ID', 'admin_' . $pluginName);

    $notFound = false;

    if (!is_string($pluginName) || ($this->container['plugins']->isEvent("my_plugin_" . $pluginName) == false)) {
        $notFound = true;
    }

    $this->container['plugins']->applyEvent("my_plugin_" . $pluginName . "_beforeHeader");

    if (!isset($_GET['hiddenHeader'])) {
        get_file_admin('header');
    }

    if (!isset($_GET['hiddenMenu'])) {
        get_page_admin('topbar');
    } else {
        $this->container['my_admin']->checkEvents($pluginName);
    }

    if($notFound)
    {
        if($this->container['plugins']->isEvent("my_plugin_" . $pluginName) == false)
        {
            header("location: " . HOST . "/my-admin/home");
            exit;
        }
    }

    $this->container['plugins']->applyEvent("my_plugin_" . $pluginName);
    if (!isset($_GET['hiddenFooter'])) {
        ?>
        <!-- /.row -->
        </div>
        <!-- /#page-wrapper -->

        </div>
        <!-- /#wrapper -->
        <?php
        get_file_admin('footer');
        ?>
        </body>

        </html>
        <?php
    }
?>

