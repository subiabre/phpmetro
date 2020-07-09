<?php

namespace PHPMetro\Analysis;

use ReflectionFunction;
use ReflectionMethod;

/**
 * Implements the non abstract methods in the AnalysisCase
 * @package PHPMetro
 * @author subiabrewd@gmail.com
 */
class BaseAnalysis implements AnalysisInterface
{
    public $sample = [];

    public $sizes = [];

    public $samplesSize = 0;

    /**
     * Create a new sample with the given size and name
     * @param string $name Sample name
     * @param int $size Sample size
     * @param callable $function Function to perform for the sample
     */
    public function addSample(string $name, int $size, callable $function): void
    {
        $reflectionFunction = new ReflectionFunction($function);
        $returnType = $reflectionFunction->getReturnType();

        if ($returnType !== null)
        {
            $this->samplesSize += $size;
            $this->sizes[$name] = $size;
            
            $i = 0;
            while ($i < $size) {
                $this->sample[$name][$i] = \call_user_func($function);

                $i++;
            }
        }
    }

    /**
     * Obtain a sample by name
     * @param string $name
     * @return array|null Returns null if the requested sample does not exist
     */
    public function getSample(string $name): ?array
    {
        if (\array_key_exists($name, $this->sample)) {
            return $this->sample[$name];
        }

        return null;
    }

    /**
     * Obtain the size of a sample after generation
     * @param string $name
     * @return int|null Returns null if the requested sample does not exist
     */
    public function getSampleSizeOf(string $name): ?int
    {
        if (\array_key_exists($name, $this->sample)) {
            return $this->sizes[$name];
        }

        return null;
    }

    /**
     * Get the total size of samples after generation
     * @param string $name
     * @return int
     */
    public function getSampleSize(): int
    {
        return $this->samplesSize;
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
            $reflectionMethod = new ReflectionMethod($this, $method);
            $returnType = $reflectionMethod->getReturnType();

            if (\preg_match('/test[A-Za-z09]*/', $method) && $returnType !== null) {
                $tests[] = $method;
            }
        }

        return $tests;
    }
}
