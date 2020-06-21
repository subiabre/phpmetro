<?php

namespace PHPMetro\Tests\Service;

use PHPMetro\Service\ConfigFinder;
use PHPUnit\Framework\TestCase;

class ConfigFinderTest extends TestCase
{
    public $finder;

    public function setUp(): void
    {
        $this->finder = new ConfigFinder();
    }

    public function testFindsConfiguration()
    {
        $config = $this->finder->load();

        $this->assertIsObject($config);
        $this->assertInstanceOf(\PHPMetro\Component\Config::class, $config);
    }

    public function testGetPath()
    {
        $this->finder->load();

        $path = $this->finder->getPath();
        $expected = \dirname(__DIR__, 2) . '/phpmetro.xml';
        
        $this->assertSame($expected ,$path);
    }
}
