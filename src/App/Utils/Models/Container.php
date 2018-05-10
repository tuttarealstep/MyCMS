<?php
/**
 * User: tuttarealstep
 * Date: 19/03/17
 * Time: 11.24
 */

namespace MyCMS\App\Utils\Models;

class Container
{
    /**
     * @var array<MyCMSDatabase|MyCMSTheme|MyCMSCache|MyCMSPlugins|MyCMSThemeCustomizer|MyCMSUsers|MyCMSSettings|MyCMSSecurity|MyCMSFunctions|MyCMSLanguages>
     * Variable who contain all loaded classes
     */
    protected $container = [];

    /**
     * Return the container variable
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

    function __construct($container)
    {
        $this->setContainer($container);
    }
}