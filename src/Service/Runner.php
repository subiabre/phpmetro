<?php

namespace PHPMetro\Service;

/**
 * Runner for PHPMetro analysis suites
 * @package PHPMetro
 * @author https://gitlab.com/subiabre
 * @license MIT
 */
class Runner
{
    private
        $root,
        $tests,
        $testsLocation,
        $config,
        $configLocation
        ;

    public function __construct($config = 'phpmetro.xml')
    {
        $this->configLocation = $this->root . $config;
        $this->config = $this->getConfig();
        
        $this->setTestsLocation($this->config->location);
    }

    /**
     * Set the root location, i.e: the location -2 at which the runner is
     */
    public function setRootLocation(string $root): self
    {
        $this->root = $root;
    }

    /**
     * Set the location of the tests location
     * @param string $location Location of the tests relative to root folder
     * @return self
     */
    public function setTestsLocation(string $location): self
    {
        $path = \realpath($this->root . $location);
        if ($path !== false && \is_dir($path))
        {
            $this->testsLocation = $path;
            
            return $this;
        }

        throw new \Exception("The provided tests location for PHPMetro does not exist.", 1);
    }

    /**
     * Return the path to the PHPMetro tests folder
     * @return string
     */
    public function getTestsLocation(): string
    {
        return $this->testsLocation;
    }

    /**
     * Return the configuration from `phpmetro.xml` config file
     * @return object
     */
    public function getConfig(): object
    {
        if (\file_exists($this->configLocation))
        {
            return \simplexml_load_file($this->root . 'phpmetro.xml');
        }

        throw new \Exception("The required phpmetro.xml config file is not present.", 1);
    }

    /**
     * Return all the tests files for PHPMetro
     * @return array
     */
    public function getTests(): array
    {
        $dir = $this->getTestsLocation();
        $handle  = \opendir($dir);
        $folders = [$dir];
        $tests = [];

        while (($filename = \readdir($handle)) !== false) {
            if ($filename != "." && $filename != ".." && \is_dir($dir . $filename))
            {
                \array_push($folders, $dir . $filename . "/");
            }
        }
        
        foreach ($folders as $dir) {
            foreach (\glob($dir . '*.php') as $filename)
            {
                 \array_push($tests, $filename);
            }
        }

        if (\count($tests) > 0) return $tests;

        throw new \Exception("The provided tests location for PHPMetro is empty.", 1);
        
    }
}
