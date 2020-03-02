<?php

namespace PHPMetro\Tests;

use PHPUnit\Framework\TestCase;

class ImaginaryAnalysisTest extends TestCase
{
    public $analysis;

    public function setUp(): void
    {
        $this->analysis = new ImaginaryAnalysis();
        $this->analysis->setUp();
    }

    public function testGetAllTests()
    {
        $tests = $this->analysis->getAllTests();

        $this->assertIsArray($tests);
        $this->assertNotEmpty($tests);
        $this->assertSame(['testTotalSum'], $tests);
    }
}
