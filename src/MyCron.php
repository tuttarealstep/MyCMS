<?php
/**
 * User: Stefano
 * Date: 27/12/2018
 * Time: 10:07
 */

ignore_user_abort( true );

if (defined( 'CRON_RUNNING' ))
{
    die();
}

define('CRON_RUNNING', true);

if(!defined('MY_CMS_PATH'))
{
    define('MY_CMS_PATH', true);
    define("LOADER_LOAD_PAGE", false);
    include 'Bootstrap.php';
}

//todo finish cron runner
/*
print_r($app);
*/