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
        $this->assertIsObject($this->test->addSample('', 0, function(){}));
    }

    public function testAddSamplePushesResultsToSampleArray()
    {
        $this->test->addSample('Test', 5, function(){
            return "";
        });

        $this->assertIsArray($this->test->sample['Test']);
        $this->assertEquals(
            5,
            \count($this->test->sample['Test'])
        );
    
        foreach ($this->test->sample['Test'] as $key => $value)
        {
            $this->assertEquals(
                "",
                $value
            );
        }
    }

    
    public function testHasBasicProperties()
    {
        $this->assertClassHasAttribute('sample', \PHPMetro\AbstractTest::class);
    }
}