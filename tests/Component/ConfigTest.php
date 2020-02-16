<?php

namespace PHPMetro\Tests\Component;

use PHPUnit\Framework\TestCase;
use PHPMetro\Component\Config;

class ConfigTest extends TestCase
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
        $this->assertInstanceOf(Config::class, $config);
    }

    public function testGetTriesDefaultLocation()
    {
        $config = $this->config->getFromFile();

        $this->assertIsObject($config);
        $this->assertInstanceOf(Config::class, $config);
    }

    public function testGetConfigReturnsGeneralConfig()
    {
        $config = $this->config->getFromFile();
        $general = $config->getConfig();
        $generalKeys = [
            'bootstrap',
            'colors',
            'namespace',
            'profiler',
            'verbose'
        ];

        $this->assertIsArray($general);
        foreach ($generalKeys as $key)
        {
            $this->assertArrayHasKey($key, $general);
        }
    }

    public function testGetSuitesReturnsAnalysisSuites()
    {
        $config = $this->config->getFromFile();
        $suites = $config->getSuites();

        $this->assertIsArray($suites);
        $this->assertIsObject($suites['Example Suite']);
        $this->assertEquals(
            'tests/statistical/ExampleSuite',
            $suites['Example Suite']->directory
        );
    }

    public function testGetThrowsExceptionOnMissingFile()
    {
        $this->expectException(\Exception::class);

        $config = $this->config->getFromFile('missing.xml');
    }
}
