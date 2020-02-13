<?php

namespace PHPMetro;

use PHPMetro\Component\Console;

/**
 * Abstract Test Case to start performing tests
 * @package PHPMetro
 * @author https://gitlab.com/subiabre
 * @license MIT
 */
abstract class AbstractTest
{
    public
        $console,
        $sample;

    public function __construct()
    {
        if (\method_exists($this, 'setUp'))
        {
            $this->setUp();
        }

        $this->console = new Console;

        $this->run();
    }

    /**
     * Override this method when running your test logic
     */
    public function run(): void
    {

    }

    /**
     * Add results to the sample array
     * @param int $iterations Number of iterations to perform
     * @param callable $function Function to be performed on each iteration \
     * it's return value will be added to the sample
     * @return self
     */
    public function addSamples(int $iterations, callable $function): self
    {
        for ($i=0; $i < $iterations; $i++) { 
            $this->sample[] = $function();
        }

        return $this;
    }
}
