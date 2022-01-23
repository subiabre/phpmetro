<?php

namespace PHPMetro\Tests\Component;

use PHPMetro\Component\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public $component;

    public function setUp(): void
    {
        $config = \dirname(__DIR__, 2) . '/phpmetro.xml';

        $this->component = new Config($config);
    }

    public function testGetsLocation()
    {
        $this->assertIsString($this->component->getLocation());
        $this->assertFileExists($this->component->getLocation());
    }

    public function testXmlIsSimpleXMLElement()
    {
        $xml = $this->component->xml;

        $this->assertIsObject($xml);
        $this->assertInstanceOf(\SimpleXMLElement::class, $xml);
    }

    public function testGetsVerboseReturnsFalse()
    {
        $this->component->xml['verbose'] = 'non empty string';

        $this->assertFalse($this->component->getVerbose());
    }

    public function testGetsPHPMetroAttributes()
    {
        $verbose = $this->component->getVerbose();

        $this->assertIsBool($verbose);
        $this->assertTrue($verbose);
    }

    public function testGetsSuites()
    {
        $suites = $this->component->getSuites();

        $this->assertIsArray($suites);
        
        foreach ($suites as $suite) {
            $this->assertIsObject($suite);
            $this->assertInstanceOf(\PHPMetro\Component\Suite::class, $suite);

            $this->assertIsString($suite->getName());
            $this->assertIsBool($suite->getIgnore());
            $this->assertIsString($suite->getSuffix());
            $this->assertIsString($suite->getNamespace());
            $this->assertIsString($suite->getDirectory());
        }
    }
}
