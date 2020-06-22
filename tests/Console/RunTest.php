<?php

namespace PHPMetro\Tests\Console;

use PHPMetro\Console\Run;
use PHPUnit\Framework\TestCase;

class RunTest extends TestCase
{
    public $runner;

    public function setUp(): void
    {
        $this->runner = new Run();
    }

    public function testCleanClassName()
    {
        $class = "\PHPMetro\Analyses\AnalysisMetro";
        $clean = $this->runner->cleanClassName($class, 'Metro', "PHPMetro\Analyses");
        $expected = "Analysis";

        $this->assertSame($expected, $clean);
    }
}
