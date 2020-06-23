<?php

namespace PHPMetro\Service;

use Exception;
use PHPMetro\Component\Config;

/**
 * Finds and loads the configuration file of PHPMetro
 * @package PHPMetro
 * @author subiabrewd@gmail.com
 */
class ConfigFinder
{
    private $configLocation;

    public $rootDir;

    public function __construct()
    {
        $traverser = new AnalysesTraverser();
        $this->rootDir = $traverser->rootDir;
    }

    /**
     * Autoload the configuration file at the root into an object
     * @return object
     */
    public function load(): Config
    {
        $file = $this->rootDir . 'phpmetro.xml';

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

        $this->configLocation = $location;
        return new Config($location);
    }

    /**
     * Obtain the location of the configuration loaded
     * @return string
     */
    public function getPath(): ?string
    {
        return $this->configLocation;
    }
}