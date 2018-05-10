<?php
/**
 * User: tuttarealstep
 * Date: 24/11/16
 * Time: 20.06
 */

namespace MyCMS\App\Utils\Plugins;

class MyCMSPlugins
{
    private static $functionIdCount = 0;

    /**
     * @var
     */
    private $container;
    private $events = [];

    private $activePlugins = [];

    /**
     * MyCMSPlugins constructor.
     * @param $container
     */
    function __construct($container)
    {
        $this->container = $container;
    }

    public function setContainer($container)
    {
        $this->container = $container;
    }

    /**
     * @param string $eventName The event name
     * @param callable $callback The callback event
     * @param int $priority 0 = First executed, 1 = normal, > 1 executed after 1
     * @param int $argumentsNumber The number of arguments
     *
     * @return true
     */
    function addEvent($eventName, $callback, $priority = 1, $argumentsNumber = 1)
    {
        $id = $this->eventUniqueId($eventName, $callback, $priority);
        $this->events[ $eventName ][ $priority ][ $id ] = ['callback' => $callback, 'argumentsNumber' => $argumentsNumber];

        return true;
    }

    /**
     * @param $tag
     * @param $function
     * @param $priority
     * @return array|bool|null|string
     */
    function eventUniqueId($tag, $function, $priority)
    {
        if (is_string($function)) {
            return $function;
        }

        if (is_object($function)) {
            $function = [$function, '#'];
        } else {
            $function = (array)$function;
        }

        if (is_object($function[0])) {
            if (function_exists('spl_object_hash')) {
                return spl_object_hash($function[0]) . $function[1];
            } else {

                $obj_id = get_class($function[0]) . $function[1];
                if (!isset($function[0]->event_id)) {
                    if (false === $priority) {
                        return false;
                    }
                    $obj_id .= isset($this->events[ $tag ][ $priority ]) ? count((array)$this->events[ $tag ][ $priority ]) : self::$functionIdCount;
                    $function[0]->event_id = self::$functionIdCount;
                    self::$functionIdCount++;
                } else {
                    $obj_id .= $function[0]->event_id;
                }

                return $obj_id;
            }
        }

        return null;
    }

    /**
     * @param $eventName
     * @param $value
     * @return mixed
     */
    function applyEvent($eventName, $value = null)
    {
        $args = [];

        if (!isset($this->events[ $eventName ]))
            return $value;

        ksort($this->events[ $eventName ]);
        reset($this->events[ $eventName ]);

        if (empty($args)) {
            $args = func_get_args(); //Get args
        }

        do {
            foreach ((array)current($this->events[ $eventName ]) as $event) {
                if ($event['callback'] != null) {
                    $args[1] = $value;
                    if(is_callable($event['callback']))
                    {
                        $value = call_user_func_array($event['callback'], array_slice($args, 1, (int)$event['argumentsNumber']));
                    } else {
                        $value = $event['callback'];
                    }
                }
            }

        } while (next($this->events[ $eventName ]) !== false);

        return $value;
    }

    /**
     * @param $eventName
     * @param $callback
     * @param int $priority
     * @return bool
     */
    function removeEvent($eventName, $callback, $priority = 1)
    {
        if (!isset($this->events[ $eventName ]))
            return false;

        $id = $this->eventUniqueId($eventName, $callback, $priority);

        unset($this->events[ $eventName ][ $priority ][ $id ]);

        return true;
    }

    function removeAllEvent($eventName)
    {
        if (!isset($this->events[ $eventName ]))
            return false;

        unset($this->events[ $eventName ]);

        return true;
    }

    function isActivePlugin($pluginName)
    {
        return in_array($pluginName, $this->activePlugins);
    }

    function activatePlugin($pluginName)
    {
        $plugin = trim($pluginName);

        if (!$this->validatePlugin($plugin) || in_array($plugin, $this->activePlugins)) {
            return false;
        }

        $pluginInfo = $this->getPlugins();

        ob_start();

        include_once($pluginInfo[ $plugin ]["dirPath"] . "/" . $pluginInfo[ $plugin ]["pluginFile"]);

        ob_end_clean();

        return null;
    }

    function validatePlugin($pluginName)
    {
        //Check information
        $installedPlugins = $this->getPlugins();
        if (!isset($installedPlugins[ $pluginName ])) {
            return false;
        }

        //Check if the plugin folder exist
        if (!file_exists($installedPlugins[ $pluginName ]["dirPath"])) {
            return false;
        }

        //Check if the plugin php file exist
        if (!file_exists($installedPlugins[ $pluginName ]["dirPath"] . "/" . $installedPlugins[ $pluginName ]["pluginFile"])) {
            return false;
        }


        return true;
    }

    /**
     * Reads the plugin in their folder
     * @param string $pluginsFolder
     * @param bool $noCache
     * @return array
     */
    function getPlugins($pluginsFolder = "", $noCache = false)
    {
        if($noCache == false)
        {
            if (!$pluginsCacheList = $this->container['cache']->get('pluginsList', 18000)) {
                $pluginsCacheList = [];
            }
        }


        if (isset($pluginsCacheList[ $pluginsFolder ])) {
            return $pluginsCacheList[ $pluginsFolder ];
        }

        $pluginsList = [];
        $pluginsListFiles = [];

        $pluginBasePath = (!empty($pluginsFolder)) ? PLUGIN_PATH . $pluginsFolder : PLUGIN_PATH;

        $pluginPath = @opendir($pluginBasePath); //FALSE: can't open

        if ($pluginPath) {
            while (false !== ($file = readdir($pluginPath))) {
                if ($file == '.' || $file == '..' || substr($file, 0, 1) == '.') {
                    continue;
                }

                if (is_dir($pluginBasePath . '/' . $file)) {
                    $pluginsSubPath = @opendir($pluginBasePath . '/' . $file); //Inside plugin-name folder
                    if ($pluginsSubPath) {
                        while (false !== ($subFile = readdir($pluginsSubPath))) {
                            if ($subFile == '.' || $subFile == '..' || substr($subFile, 0, 1) == '.') {
                                continue;
                            }

                            if ($subFile == "info.json") {
                                $pluginsListFiles[] = "$file/$subFile";
                            }
                        }
                        closedir($pluginsSubPath);
                    }
                }
            }
            closedir($pluginPath);
        }

        if (empty($pluginsListFiles)) {
            return $pluginsList;
        }

        foreach ($pluginsListFiles as $pluginsListInfoFile) {

            if (!is_readable("$pluginBasePath/$pluginsListInfoFile")) {
                continue;
            }

            $pluginInfo = $this->getPluginInfo("$pluginBasePath/$pluginsListInfoFile");

            if (empty($pluginInfo['name']) || (!file_exists(dirname("$pluginBasePath/$pluginsListInfoFile") . "/" . $pluginInfo['pluginFile']))) {
                continue;
            }

            $pluginsList[ basename(dirname("$pluginBasePath/$pluginsListInfoFile")) ] = array_merge($pluginInfo, ["dirPath" => dirname("$pluginBasePath/$pluginsListInfoFile")]);
        }


        $cachePlugins[ $pluginsFolder ] = $pluginsList;
        $this->container['cache']->set('pluginsList', $cachePlugins);

        return $pluginsList;
    }

    /**
     * return Plugin info from info.json
     * @param $pluginFilePath
     * @return array|mixed
     */
    function getPluginInfo($pluginFilePath)
    {
        $infoArray = [];
        if (file_exists($pluginFilePath)) {
            $infoFile = file_get_contents($pluginFilePath);
            $infoArray = json_decode($infoFile, true);
        }

        return $infoArray;
    }

    function deactivatePlugin($pluginName)
    {
        $plugin = trim($pluginName);

        if (!$this->validatePlugin($plugin) || in_array($plugin, $this->activePlugins)) {
            return false;
        }

        $pluginInfo = $this->getPlugins();

        unset($pluginInfo[ $plugin ]);

        return null;
    }

    function isEvent($event)
    {
        if (isset($this->events[ $event ])) {
            return true;
        }

        return false;
    }

    //todo make plugin page in admin panel to download plugin and activate them or install ...
}