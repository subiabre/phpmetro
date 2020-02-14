<?php

namespace PHPMetro;

use PHPMetro\Component\Console;
use PHPMetro\AnalysisInterface;

/**
 * Abstract Analysis to start performing measures
 * @package PHPMetro
 * @author https://gitlab.com/subiabre
 * @license MIT
 */
abstract class Analysis implements AnalysisInterface
{
    public
        $console,
        $sample;

    public function __construct()
    {
        $this->console = new Console;

        $this->setUp();
        $this->runTests();
    }

    /**
     * This method will run before your test, you'd want to prepare your samples here
     */
    public function setUp(): void
    {

    }

    /**
     * Run all the tests
     */
    private function runTests(): void
    {
        $methods = \get_class_methods($this);
        $tests = \preg_grep('/test[_A-Za-z1-9]/', $methods);
        $results = [];
        
        foreach ($tests as $test)
        {
            if (NULL !== $this->{$test}()) {
                $results[\substr($test, 4)] = $this->{$test}();
            }
        }
    
        $this->console->write($this->console->arrayToString($results));
    }

    /**
     * Add a new sample record
     * @param string $name Name of this sample
     * @param int $size Number of iterations to perform the function
     * @param callable $function Function to be performed on each iteration \
     * it's return value will be added to the sample
     * @return self
     */
    public function addSample(string $name, ?int $size, callable $function): self
    {
        for ($i=0; $i < (int) $size; $i++) {
            if (NULL !== $function()) {
                $this->sample[$name][$i] = $function();
            }
        }

        return $this;
    }
}
