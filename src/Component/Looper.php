<?php

namespace PHPMetro\Component;

/**
 * Produces iterations and loops
 * @package PHPMetro
 * @author https://gitlab.com/subiabre
 * @license MIT
 */
class Looper
{
    /**
     * Number of the current loop iteration
     * @var int
     */
    private $iteration = 0;

    /**
     * Obtain the number of the iteration. \
     * You may want to use this function inside loops
     * @return int
     */
    public function getIteration(): int
    {
        return $this->iteration;
    }

    /**
     * Repeat a given function the specified number of times
     * @param int $iterations Number of loops to do
     * @param callable $function The function to be looped
     * @return void
     */
    public function for(int $iterations, callable $function): void
    {
        for ($i=0; $i < $iterations; $i++) {
            $this->iteration = $i;

            $function();
        }
    }
}
