<?php

namespace PHPMetro\Tests\Analysis;

use PHPMetro\Analysis\BaseAnalysis;
use PHPUnit\Framework\TestCase;

class BaseAnalysisTest extends TestCase
{
    public $case;

    public function setUp(): void
    {
        $this->case = new BaseAnalysis();
    }

    public function testAddSampleCreatesSampleWithLength()
    {
        $this->case->addSample('Filled', 10, function(): string {
            return '';
        });

        $filled = $this->case->getSample('Filled');

        $this->assertArrayNotHasKey('Empty', $this->case->sample);

        $this->assertIsArray($filled);
        $this->assertEquals(10, \count($filled));
    }

    public function testAddSampleThrowsException()
    {
        $this->expectException(\Exception::class);

        $this->case->addSample('Empty', 10, function() {
            // functions that don't return values don't create a sample
        });
    }

    public function testAddSampleSavesTheSampleLength()
    {
        $this->case->addSample('Filled', 10, function(): string {
            return '';
        });

        $this->assertSame(10, $this->case->getSampleSizeOf('Filled'));
        $this->assertSame(10, $this->case->getSampleSize());
    }

    public function testGetsAllTestsAsAnArray()
    {
        $tests = $this->case->getAllTests();

        $this->assertIsArray($tests);
        $this->assertEmpty($tests);
    }

    public function testGetSampleReturnsNullOnNoSample()
    {
        $sample = $this->case->getSample('unexisting');
        $size = $this->case->getSampleSizeOf('unexisting');

        $this->assertNull($sample);
        $this->assertNull($size);
    }
}
