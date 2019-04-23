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

$cronArray = $app->container['cron']->getCronArray();

foreach ($cronArray as $hook => $cronJob)
{
    if(isset($cronJob['running']) && $cronJob['running'] == true)
    {
        if(!$app->container['cron']->parseCronExpression(time(), $cronJob['cronTime']))
            $cronArray[$hook]['running'] = false;
        continue;
    }

    if($cronJob['startFrom'] != null)
    {
        if(time() < $cronJob['startFrom'])
        {
            continue;
        }
    }

    if($app->container['cron']->parseCronExpression(time(), $cronJob['cronTime']))
    {
        $app->container['plugins']->applyEvent($hook);

        if($cronJob['runOnce'])
        {
            $app->container['cron']->removeScheduledEvent($hook);
        } else {
            $cronArray[$hook]['running'] = true;
        }
    } else {
        $cronArray[$hook]['running'] = false;
    }
}
$app->container['cron']->cronArray = $cronArray;
$app->container['cron']->saveCron();