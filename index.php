<?php
/**
 *  @author Stefano Valenzano - Tuttarealstep
 *  @package MyCMS
 */

define('MY_CMS_PATH', true);
define('FILE', dirname( __FILE__ ));
define('CONFIG_FILE', '/src/App/Configuration/');

$disableInstall = false;
if($disableInstall == false && !file_exists(FILE.CONFIG_FILE.'my_config.php'))
{
    header("location: ../src/App/Content/Installer/index.php");
    exit;
} else {
    require_once( dirname( __FILE__ ) . '/src/Bootstrap.php' );
}