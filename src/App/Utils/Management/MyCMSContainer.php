<?php

    namespace MyCMS\App\Utils\Management;

    if (!defined("MY_CMS_PATH")) {
        die("NO SCRIPT");
    }

    /**
     * Class MyCMSContainer
     * @package MyCMS\App\Utils\Management
     */
    class MyCMSContainer
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