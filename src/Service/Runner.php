<?php

namespace PHPMetro\Service;

use PHPMetro\Component\Console;
use PHPMetro\Component\Config;

/**
 * Runner service for PHPMetro
 * @package PHPMetro
 * @author https://gitlab.com/subiabre
 * @license MIT
 */
class Runner
{
    private
        $console,
        $config,
        $customs,

        $tests
        ;

    public function __construct()
    {
        $this->console = new Console();
        $this->config = new Config();
    }

    /**
     * Set custom console parameters
     * @param array
     */
    public function customs(?array $params = NULL): void
    {
        if (\count($params) > 0)
        {
            $this->customs = $params;
        }
    }

    /**
     * Obtain the configuration object
     * @return object
     */
    public function getConfig(): object
    {
        $location = NULL;
        if ($this->customs['c']) $location = $this->customs['c'];

        return $this->config->getFromFile($location);
    }

    /**
     * Set the location of the tests folder
     * @param string $location
     * @return void
     */
    public function setTestsLocation($location): void
    {
        if (\file_exists($location) && \is_dir($location))
        {
            $this->tests = $location;
        }
    }

    /**
     * Obtain the tests location if this was defined and exists
     * @return string
     */
    public function getTestsLocation(): ?string
    {
        return $this->tests;
    }

    /**
     * Obtain the analysis classes looking recursively through the analysis directory
     * @param string $directory Location of the suite directory
     * @return array
     */
    public function getTests(string $directory): array
    {
        $files = \glob($directory . '*.php');
        $tests = [];

        foreach ($files as $file)
        { 
            $tests[] = \basename($file);
        }

        return $tests;
    }

    /**
     * Start the runner routine
     * @return void
     */
    public function run(): void
    {
        $xml = $this->getConfig();
        $config = $xml->getConfig();
        include $config['bootstrap'];

        $this->console->write("PHPMetro by Facundo Subiabre.");

        $suites = $xml->getSuites();
        foreach ($suites as $name => $suite)
        {
            $this->console->write("Running {$name}");

            $tests = $this->getTests($suite->directory);
            foreach ($tests as $test)
            {
                $do = new $config['namespace'] . $test;

                $do->setUp();

                if ($config['verbose']) {
                    $do->setVerboseRunning();
                }

                $do->runTests();
            }
        }
    }
}
