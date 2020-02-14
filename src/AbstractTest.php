<?php

namespace PHPMetro;

use PHPMetro\Component\Console;
use PHPMetro\TestInterface;

/**
 * Abstract Test Case to start performing tests
 * @package PHPMetro
 * @author https://gitlab.com/subiabre
 * @license MIT
 */
abstract class AbstractTest implements TestInterface
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

        foreach ($methods as $method)
        {
            \preg_match('/test[_A-Za-z0-9]*[()]*/', $method, $tests);
        }

        foreach ($tests as $test)
        {
            $this->{$test}();
        }
    }

    /**
     * Add a new sample record
     * @param string $name Name of this sample
     * @param int $size Number of iterations to perform the function
     * @param callable $function Function to be performed on each iteration \
     * it's return value will be added to the sample
     * @return self
     */
    public function addSample(string $name, int $size, callable $function): self
    {
        for ($i=0; $i < $size; $i++) { 
            $this->sample[$name][$i] = $function();
        }

        return $this;
    }
}
