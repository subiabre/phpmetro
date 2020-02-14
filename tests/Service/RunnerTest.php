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
        $tests = $this->runner->getTests();

        $this->expectException(Exception::class);
    }

    public function testRunnerExpectsConfigFile()
    {
        $config = $this->runner->getConfig();

        $this->expectException(Exception::class);
    }
}
