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
    public $cronArray = [];
    public $currentJobs = [];

    function __construct($container)
    {
        $this->setContainer($container);

        if ($this->container['settings']->getSettingsValue('cron') == false) {
            $this->container['settings']->addSettingsValue('cron', serialize([]));
        }

        $this->cronArray = unserialize(base64_decode($this->container['settings']->getSettingsValue('cron')));
    }

    /**
     * @param $hook
     * @param $cronTime
     * @param null $startFrom
     * @param bool $runOnce
     * @return bool
     */
    function scheduleEvent($hook, $cronTime, $startFrom = null, $runOnce = false)
    {
        if(isset($this->cronArray[$hook]))
            return false;

        $cronTime = $this->parseCronTime($cronTime);

        $tmpArray = [
            'cronTime' => $cronTime,
            'startFrom' => $startFrom,
            'runOnce' => $runOnce
        ];

        $this->cronArray[$hook] = $tmpArray;
        $this->saveCron();
        return true;
    }

    /**
     * @param $hook
     * @return bool
     */
    function removeScheduledEvent($hook)
    {
        if(!isset($this->cronArray[$hook]))
            return false;

        unset($this->cronArray[$hook]);
       // $this->saveCron();
        return true;
    }

    function saveCron()
    {
        $this->container['settings']->saveSettings('cron', base64_encode(serialize($this->cronArray)));
    }

    function parseCronExpression($time, $cronExpression)
    {
        $time = explode(' ', date('i G j n w', $time));
        $cronExpression = explode(' ', $cronExpression);
        foreach ($cronExpression as $key => &$value)
        {
            $time[$key] = intval($time[$key]);
            $value = explode(',', $value);
            foreach ($value as &$valueTmp) {
                $valueTmp = preg_replace(
                    [
                        '/^\*$/',
                        '/^\d+$/',
                        '/^(\d+)\-(\d+)$/',
                        '/^\*\/(\d+)$/'
                    ],
                    [
                        'true',
                        $time[$key] . '===\0',
                        '(\1<=' . $time[$key] . ' and ' . $time[$key] . '<=\2)',
                        $time[$key] . '%\1===0'
                    ],
                    $valueTmp
                );
            }
            $value = '(' . implode(' or ', $value) . ')';
        }
        $cronExpression = implode(' and ', $cronExpression);
        return eval('return ' . $cronExpression . ';');
    }

    /**
     * @param $cronTime
     * @return bool|string
     */
    private function parseCronTime($cronTime)
    {
        switch ($cronTime) {
            case 'annually':
            case 'yearly':
                return "0 0 1 1 *";
                break;
            case 'monthly':
                return "0 0 1 * *";
                break;
            case 'weekly':
                return "0 0 * * 0";
                break;
            case 'midnight':
            case 'daily':
                return "0 0 * * *";
                break;
            case 'hourly':
                return "0 * * * *";
                break;
            default:
                return $cronTime;
        }
    }

    function getCronArray()
    {
        return $this->cronArray;
    }
}
//todo finish cron class