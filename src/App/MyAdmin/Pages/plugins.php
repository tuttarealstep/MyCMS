<?php
$this->container['users']->hideIfNotLogged();

define('PAGE_ID', 'admin_plugins');
define('PAGE_NAME', $this->container['languages']->ta('page_plugins_header', true));

if(!$this->container['users']->currentUserHasPermission("manage_plugins"))
{
    throw new MyCMS\App\Utils\Exceptions\MyCMSException("You do not have permission to access this page!", "Permission denied");
}

if(isset($_GET['action']))
{
    switch ($_GET['action'])
    {
        case 'enable':
            if(isset($_GET['name']) && $_GET['name'] != null) {
                if ($this->container['settings']->getSettingsValue('active_plugins') == false) {
                    $this->container['settings']->saveSettings('active_plugins', base64_encode(serialize([$_GET['name']])), true);
                } else {
                    $activePlugins = unserialize(base64_decode($this->container['settings']->getSettingsValue('active_plugins')));

                    if (!in_array($_GET['name'], $activePlugins)) {
                        $activePlugins = array_merge($activePlugins, [$_GET['name']]);
                        $this->container['settings']->saveSettings('active_plugins', base64_encode(serialize($activePlugins)));
                    }
                }
            }

            header("location: " . HOST . "/my-admin/plugins");
            exit();
            break;
        case 'disable':
            if ($this->container['settings']->getSettingsValue('active_plugins') != false)
            {
                $activePlugins = unserialize(base64_decode($this->container['settings']->getSettingsValue('active_plugins')));

                foreach ($activePlugins as $key => $value)
                {
                    if($value == $_GET['name'])
                    {
                        unset($activePlugins[$key]);
                    }
                }

                $this->container['settings']->saveSettings('active_plugins', base64_encode(serialize($activePlugins)));

                header("location: " . HOST . "/my-admin/plugins");
                exit();
            }
            break;
    }
}

$this->getFileAdmin('header');
$this->getPageAdmin('topbar');
?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="h1PagesTitle"><?php $this->container['languages']->ta('page_plugins_header'); ?></h1>
        </div>
    </div>
    <form action="" method="post">
        <div class="row">
            <div class="col-lg-12">
                <table class="table table-striped table-bordered table-hover" id="tables_posts">
                    <thead>
                    <tr>
                        <th><?php $this->container['languages']->ta('page_plugins_table_name'); ?></th>
                        <th><?php $this->container['languages']->ta('page_plugins_table_description'); ?></th>
                        <th><?php $this->container['languages']->ta('page_plugins_manage'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $plugins = $this->container['plugins']->getPlugins("", true);

                    $activePlugins = [];
                    $dbActivePlugins = $this->container['settings']->getSettingsValue('active_plugins');
                    if($dbActivePlugins != false) {
                        $dbActivePlugins = unserialize(base64_decode($dbActivePlugins));
                        if (is_array($dbActivePlugins)) {
                            $activePlugins = $dbActivePlugins;
                        }
                    }

                    foreach ($plugins as $pluginName => $pluginValues)
                    {
                        ?>
                        <tr>
                            <td><?php echo $pluginValues['name']; ?></td>
                            <td><?php echo $pluginValues['description']; ?></td>
                            <?php
                        if(in_array($pluginName, $activePlugins) || in_array($pluginName, $this->container['pluginsManualActivate']))
                        {
                            ?>
                            <td><a href="{@siteURL@}/my-admin/plugins?action=disable&name=<?php echo $pluginName; ?>" style="text-decoration: underline"><?php $this->container['languages']->ta('page_plugins_disable'); ?></a></td>
                            <?php
                        } else {
                            ?>
                            <td><a href="{@siteURL@}/my-admin/plugins?action=enable&name=<?php echo $pluginName; ?>" style="text-decoration: underline"><?php $this->container['languages']->ta('page_plugins_enable'); ?></a></td>
                            <?php
                        }
                        ?>
                        </tr>
                        <?php
                    }

                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>
<?php $this->getFileAdmin('footer'); ?>

