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
     * @return Config
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
     * Load the configuration file at the given location
     * @param string $path Path to configuration file
     * @return Config|null Returns null if no config file was found
     */
    public function loadFrom(string $path): ?Config
    {
        $location = $path;
        if (\is_dir($path)) $location = \rtrim($path, '/\/\\') . '/phpmetro.xml';

        if (!\file_exists($location)) {
            throw new Exception("The configuration file at '$location' could not be retrieved.", 1);
            return null;
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