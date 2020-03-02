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
     * Load the configuration file into an object
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

        if (!isset($location)) {
            echo "PHPMetro config file not found at root location." . PHP_EOL;

            $template = \file($this->rootDir . 'vendor/subiabre/phpmetro/phpmetro.xml');

            \file_put_contents($this->rootDir . 'phpmetro.xml', $template);

            echo "Copied the template config to the project root." . PHP_EOL;
            echo "Please review and edit this file before running PHPMetro again." . PHP_EOL;

            die(1);
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