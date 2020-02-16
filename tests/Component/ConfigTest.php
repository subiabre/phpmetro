<?php

namespace PHPMetro\Tests\Component;

use PHPUnit\Framework\TestCase;
use PHPMetro\Component\Config;

class ConfigTests extends TestCase
{
    public $config;

    public function setUp(): void
    {
        $this->config = new Config();
    }

    public function testSetsDefaultLocationOnEmptyConstructor()
    {
        $location = $this->config->getFilePath();

        $this->assertIsString($location);
        $this->assertEquals(
            './phpmetro.xml',
            $location
        );
    }

    public function testGetFromFileReturnsObject()
    {
        $config = $this->config->getFromFile('./phpmetro.xml');

        $this->assertIsObject($config);
        $this->assertInstanceOf(\SimpleXMLElement::class, $config);
    }

    public function testGetTriesDefaultLocation()
    {
        $config = $this->config->getFromFile();

        $this->assertIsObject($config);
        $this->assertInstanceOf(\SimpleXMLElement::class, $config);
    }

    public function testGetThrowsExceptionOnMissingFile()
    {
        $this->expectException(\Exception::class);

        $config = $this->config->getFromFile('missing.xml');
    }
}
