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
        $tests = $this->runner->getTests('./tests');

        $this->assertIsArray($tests);
    }

    public function testRunnerGetsAnalysisFiles()
    {
        $tests = $this->runner->getTests('./tests');

        $this->assertIsArray($tests);

        // There are no Analysis of PHPMetro
        $this->assertEquals(0, \count($tests));
    }

    public function testRunnerExpectsConfigFile()
    {
        $config = $this->runner->getConfig();

        $this->assertIsObject($config);
    }
}
