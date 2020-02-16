<?php

namespace PHPMetro\Tests\Component;

use PHPUnit\Framework\TestCase;
use PHPMetro\Component\Config;

class ConfigTests extends TestCase
{
    public $config;

    public function setUp(): void
    {
        $this->$config = new Config();
    }

    public function testGetFromFileReturnsObject()
    {
        $config = $this->config->getFromFile(\dirname(__DIR__, 2) . '/phpmetro.xml');

        $this->assertIsObject($config);
        $this->assertInstanceOf(\SimpleXMLElement::class, $config);
    }

    public function testGetThrowsExceptionOnMissingFile()
    {
        $this->expectException(\Exception::class);

        $config = $this->config->getFromFile('phpmetro.xml');
    }
}
