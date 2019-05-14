<?php
/**
 * User: tuttarealstep
 * Date: 29/11/16
 * Time: 19.32
 */

namespace MyCMS\App\Utils\Management;

use DirectoryIterator;

/**
 * Class MyCMSCache
 * @package MyCMS\App\Utils\Management
 */
class MyCMSCache
{
    private $container;

    /**
     * MyCMSCache constructor.
     * @param $container
     */
    function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * @param $key
     * @param $value
     * @param int $expireTime in ms
     */
    function set($key, $value, $expireTime = 0)
    {
        $value = serialize($value);
        if ($expireTime > 0) {
            file_put_contents(C_PATH . "/Storage/Cache/" . md5($key), '<?php $expireTime = ' . (time() + ($expireTime) / 1000) . '; $value = \'' . $value . '\';');
        } else {
            file_put_contents(C_PATH . "/Storage/Cache/" . md5($key), '<?php $value = \'' . $value . '\';');
        }
    }

    /**
     * Get the current cached value identified by the key
     *
     * @param $key
     * @return bool|mixed
     */
    function get($key)
    {
        @include C_PATH . "/Storage/Cache/" . md5($key);

        if (isset($value) && isset($expireTime) && ($expireTime < time())) {
            $bckValue = isset($value) ? @unserialize($value) : false;
            @unlink(C_PATH . "/Storage/Cache/" . md5($key));

            return $bckValue;
        }

        return isset($value) ? @unserialize($value) : false;
    }

    /**
     * Delete a saved value
     * @param $key
     * @return bool
     */
    function clear($key)
    {

        if (file_exists(C_PATH . "/Storage/Cache/" . md5($key))) {
            @unlink(C_PATH . "/Storage/Cache/" . md5($key));

            return true;
        }

        return false;
    }

    /**
     * Remove all caches
     */
    function clearAll()
    {
        try {
            foreach (new DirectoryIterator(C_PATH . "/Storage/Cache/") as $fileInfo) {
                if (!$fileInfo->isDot()) {
                    if($fileInfo->getFilename() == ".tmp")
                    {
                        continue;
                    }

                    unlink($fileInfo->getPathname());
                }
            }

            return [true, null];
        } catch (\Exception $exception)
        {
            return [false, $exception];
        }
    }
}