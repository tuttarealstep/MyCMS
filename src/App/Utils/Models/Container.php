<?php
/**
 * User: tuttarealstep
 * Date: 19/03/17
 * Time: 11.24
 */

namespace MyCMS\App\Utils\Models;

if (!defined("MY_CMS_PATH")) {
    die("NO SCRIPT");
}

class Container
{
    /**
     * @var array
     * Variable who contain all loaded classes
     */
    protected $container = [];

    /**
     * Return the container variable
     *
     * @return array
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Set the container variable
     * @param $container
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }
}