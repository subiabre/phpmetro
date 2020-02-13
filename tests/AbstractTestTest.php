<?php

namespace PHPMetro\Tests;

use PHPUnit\Framework\TestCase;
use PHPMetro\AbstractTest;

class AbstractTestTest extends TestCase
{
    public $test;

    public function setUp(): void
    {
        $this->test = $this->getMockForAbstractClass('PHPMetro\AbstractTest');
    }

    public function testItHasTheInternalComponents()
    {
        $this->assertIsObject($this->test->console);
        $this->assertInstanceOf(\PHPMetro\Component\Console::class, $this->test->console);
    }

    public function testHasBasicFunctions()
    {
        $this->assertIsObject($this->test->addSamples(0, function(){}));
    }

    public function testAddSamplesPushesResultsToSampleArray()
    {
        $this->test->addSamples(5, function(){
            return "";
        });

        $this->assertIsArray($this->test->sample);
        $this->assertEquals(
            5,
            \count($this->test->sample)
        );
        $this->assertEquals(
            "",
            $this->test->sample[0]
        );
    }

    public function testHasBasicProperties()
    {
        $this->assertClassHasAttribute('sample', \PHPMetro\AbstractTest::class);
    }
}
