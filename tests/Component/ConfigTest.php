<?php

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

    public function testXmlIsSimpleXMLElement()
    {
        $xml = $this->component->xml;

        $this->assertIsObject($xml);
        $this->assertInstanceOf(\SimpleXMLElement::class, $xml);
    }

    public function testGetsPHPMetroAttributes()
    {
        $verbose = $this->component->getVerbose();
        $bootstrap = $this->component->getBootstrap();

        $this->assertIsBool($verbose);
        $this->assertIsString($bootstrap);
        $this->assertFileExists($bootstrap);
    }

    public function testGetsSuites()
    {
        $suites = $this->component->getSuites();

        $this->assertIsArray($suites);
        
        foreach ($suites as $suite) {
            $name = $suite->getName();

            $this->assertIsObject($suite);
            $this->assertIsString($name);
        }
    }
}
