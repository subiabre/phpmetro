<?php

namespace PHPMetro\Service;

use PHPMetro\Component\Config;

/**
 * Finds and loads the configuration file of PHPMetro
 * @package PHPMetro
 * @author subiabrewd@gmail.com
 */
class ConfigFinder
{
    public $rootDir;

    public function __construct()
    {
        $traverser = new AnalysesTraverser();
        $this->rootDir = $traverser->rootDir;
    }

    /**
     * Load the configuration file into an object
     * @return object
     */
    public function load(): Config
    {
        $file = $this->rootDir . '/phpmetro.xml';

        $configs = [
            $file . '.local',
            $file . '.dist',
            $file
        ];

        foreach ($configs as $config)
        {
            if (\file_exists($config)) {
                $location = $config;

                break;
            }
        }

        return new Config($location);
    }
}