<?php
/**
 *  @author Stefano V. - Tuttarealstep
 *  @package MyCMS
 */

define('MY_CMS_PATH', true);
define('FILE', dirname( __FILE__ ));
define('MY_INSTALL', '/install/');

$disableInstall = false;

if($disableInstall == false && file_exists(FILE.MY_INSTALL.'index.php'))
{
    header("location: ../install/index.php");
    exit;
} else {
    require_once( dirname( __FILE__ ) . '/src/Bootstrap.php' );
}