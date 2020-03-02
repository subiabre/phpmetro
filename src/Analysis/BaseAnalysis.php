<?php

namespace PHPMetro\Analysis;

/**
 * Implements the non abstract methods in the AnalysisCase
 * @package PHPMetro
 * @author subiabrewd@gmail.com
 */
class BaseAnalysis
{
    /**
     * Contains the state of the analysis set up
     * @var bool
     */
    public $isSettingUp = false;

    protected $sample;

    /**
     * Create a new sample with the given size and name
     * @param string $name Sample name
     * @param int $size Sample size
     * @param callable $function Function to perform for the sample
     */
    public function addSample(string $name, int $size, callable $function): void
    {
        for ($i=1; $i < $size; $i++) { 
            $this->isSettingUp = true;
            
            if ($function !== null) {
                $this->sample[$name][$i - 1] = $function();
            }
        }

        $this->isSettingUp = false;
    }

    /**
     * Obtain a sample by name
     * @param string $name
     * @return array
     */
    public function getSample(string $name): array
    {
        return $this->sample[$name];
    }

    /**
     * Obtain an array of all the test methods
     * @return array
     */
    public function getAllTests(): array
    {
        $methods = \get_class_methods($this);
        $tests = [];

        foreach ($methods as $method)
        {
            if (\preg_match('/test[A-Za-z09]*\(\)/', $method) && $this->{$method}() !== null) {
                $tests[] = $method;
            }
        }

        return $tests;
    }
}
