<?php

namespace PHPMetro\Tests\Service;

use PHPUnit\Framework\TestCase;
use PHPMetro\Service\Runner;

class RunnerTest extends TestCase
{
    public $runner;

    public function setUp(): void
    {
        $this->runner = new Runner();
    }

    public function testRunnerSearchesLocation()
    {
        $this->runner->setTestsLocation('tests');

        $location = $this->runner->getTestsLocation();

        $this->assertDirectoryExists($location);
    }

    public function testRunnerExpectsTests()
    {
        $this->expectException(\Exception::class);

        $tests = $this->runner->getTests();
    }

    public function testRunnerExpectsConfigFile()
    {
        $config = $this->runner->getConfig();

        $this->assertIsObject($config);
    }
}
