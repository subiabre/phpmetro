<?php

namespace PHPMetro\Analysis;

use Closure;
use ReflectionFunction;
use ReflectionMethod;

use function Amp\ParallelFunctions\parallelMap;
use function Amp\Promise\wait;

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

    const POOL_MAX_SIZE = 65536;

    /**
     * Create a new sample with the given size and name
     * @param string $name Sample name
     * @param int $size Sample size
     * @param callable $function Function to perform for the sample
     */
    public function addSample(string $name, int $size, Closure $function): void
    {
        $reflectionFunction = new ReflectionFunction($function);
        $returnType = $reflectionFunction->getReturnType();

        if ($returnType === null)
        {
            throw new \Exception("Sampling function must specify a return value.", 1);
            return;
        }

        $sample = [];
        $pools = wait(parallelMap(
            $this->getPools($size),
            function ($size) use ($function) {
                $pool = [];

                $i = 0;
                while ($i < $size) {
                    $pool[] = $function();

                    $i++;
                }

                return $pool;
            }
        ));

        foreach ($pools as $pool) {
            $sample = \array_merge($sample, $pool);
        }

        $sampleSize = \count($sample);

        $this->samplesSize += $sampleSize;
        $this->sizes[$name] = $sampleSize;
        $this->sample[$name] = $sample;
    }

    /**
     * Fill as many pools as necessary to drain the given water
     * @param int $size
     */
    private function getPools(int $water): array
    {
        $pools = [];

        while ($water > 0) {
            if ($water > self::POOL_MAX_SIZE) {
                $pools[] = self::POOL_MAX_SIZE;
                $water -= self::POOL_MAX_SIZE;
                continue;
            }

            $pools[] = $water;
            $water = 0;
        }

        return $pools;
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
