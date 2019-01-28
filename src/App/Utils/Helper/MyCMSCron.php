<?php
/**
 * User: Stefano
 * Date: 27/12/2018
 * Time: 10:14
 */

namespace MyCMS\App\Utils\Helper;

use MyCMS\App\Utils\Management\MyCMSContainer;

class MyCMSCron extends MyCMSContainer
{
    function __construct($container)
    {
        $this->setContainer($container);
    }

    function schedule_event($time, $rule, $hook, $args = [])
    {

        //$this->get_cron_array()
        //$this->container['plugins']->addEvent('cron_array', []);
    }

    function remove_scheduled_event($hook)
    {

    }

    function get_cron_array()
    {
        return $this->container['settings']->getSettingsValue('cron');
    }
}
//todo finish cron class