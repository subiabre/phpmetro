<?php

namespace PHPMetro\Tests\Component;

use PHPMetro\Component\Suite;
use PHPUnit\Framework\TestCase;
use SimpleXMLElement;

class SuiteTest extends TestCase
{
    public $suite;

    public function setUp(): void
    {
        $this->suite = new Suite(new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><phpmetro/>'));
    }

    public function testGetsDefaults()
    {
        $this->assertSame(false, $this->suite->getIgnore());
        $this->assertSame('Analysis', $this->suite->getSuffix());
    }
}
