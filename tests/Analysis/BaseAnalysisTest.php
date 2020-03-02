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
