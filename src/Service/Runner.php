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
     * @param string $namespace Namespace of the classes to be analysed
     * @return array
     */
    public function getTests(string $directory, string $namespace = ''): array
    {
        $dir = new \RecursiveDirectoryIterator($directory);
        $ite = new \RecursiveIteratorIterator($dir);
        $files = new \RegexIterator($ite, '/[A-Za-z]*\.php/', \RegexIterator::GET_MATCH);
        $fileList = [];

        foreach ($files as $key => $file)
        {
            $classname = '\\' . \trim($namespace, '\\') . '\\';
            $classname .= \str_replace('/', '\\', \ltrim(\rtrim($key, '.php'), $directory));

            $fileList[] = [
                'file' => $key,
                'classname' => $classname
            ];
        }

        return $fileList;
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

        $this->console->write("PHPMetro by Facundo Subiabre.\n");

        $suites = $xml->getSuites();
        foreach ($suites as $name => $suite)
        {
            $this->console->write("Running {$name}." . PHP_EOL);
            $this->console->write(PHP_EOL);

            $tests = $this->getTests($suite->directory, $config['namespace']);

            $start = \microtime(true);
            foreach ($tests as $test)
            {
                include $test['file'];

                $do = new $test['classname']();

                $analysis = \ltrim($test['classname'], $config['namespace']);
                $this->console->write("\t{$analysis}: ");

                $do->setUp();
                if ($config['verbose'] === "true")
                {
                    $samples = \count($do->sample);
                    $size = 0;

                    foreach ($do->sample as $sample)
                    {
                        $size += \count($sample);
                    }

                    $this->console->write("{$samples} samples with {$size} records.");
                }
                $this->console->write(PHP_EOL);

                $do->runTests();
            }
            $end = \microtime(true);

            if ($config['verbose'] === "true")
            {
                $time = \substr($end - $start, 0, 5);
                $testsCount = \count($tests);

                $this->console->write(PHP_EOL);
                $this->console->write("Performed {$testsCount} analysis in {$time} seconds.");
                $this->console->write(PHP_EOL);
            }
        }
    }
}
