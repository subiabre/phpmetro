<?php

namespace PHPMetro\Analysis;

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
        $this->case->addSample('Empty', 10, function() {
            // functions that don't return values aren't added to the sample
        });
        $this->case->addSample('Filled', 10, function() {
            return '';
        });

        $empty = $this->case->getSample('Empty');
        $filled = $this->case->getSample('Filled');

        $this->assertIsArray($empty);
        $this->assertEmpty($empty);

        $this->assertIsArray($filled);
        $this->assertEquals(10, \count($filled));
    }

    public function testAddSampleSavesTheSampleLength()
    {
        $this->case->addSample('Empty', 10, function() {
            // functions that don't return values aren't added to the sample
        });
        $this->case->addSample('Filled', 10, function() {
            return '';
        });

        $this->assertSame(0, $this->case->getSampleSizeOf('Empty'));
        $this->assertSame(10, $this->case->getSampleSizeOf('Filled'));
    }

    public function testAddSampleFlagsSettingUp()
    {
        $this->assertFalse($this->case->isSettingUp);

        $this->case->addSample('Test', 10, function(){
            $this->assertTrue($this->case->isSettingUp);
        });

        $this->assertFalse($this->case->isSettingUp);
    }

    public function testGetsAllTestsAsAnArray()
    {
        $tests = $this->case->getAllTests();

        $this->assertIsArray($tests);
        $this->assertEmpty($tests);
    }
}
