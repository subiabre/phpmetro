<?php

namespace PHPMetro\Tests\Service;

use PHPMetro\Service\AnalysesTraverser;
use PHPUnit\Framework\TestCase;

class AnalysesTraverserTest extends TestCase
{
    public $traverser;

    public function setUp(): void
    {
        $this->traverser = new AnalysesTraverser();
    }

    public function testLocatesRoot()
    {
        $path = \dirname(__DIR__, 2) . '/';
        $root = $this->traverser->rootDir;

        $this->assertEquals($path, $root);
    }

    public function testLocatesDirectory()
    {
        $this->traverser->setDirectory('./tests');

        $directory = $this->traverser->getDirectory();

        $this->assertIsString($directory);
        $this->assertDirectoryExists($directory);
    }

    public function testGetClassesReturnsArray()
    {
        $this->traverser->setDirectory('tests');

        $tests = $this->traverser->getClasses();

        $this->assertIsArray($tests);
        $this->assertNotEmpty($tests);
    }

    public function testGetClassesWithSuffix()
    {
        $this->traverser->setDirectory('/tests');
        $this->traverser->setSuffix('NonExistentSuffix');

        $tests = $this->traverser->getClasses();

        $this->assertIsArray($tests);
        $this->assertEmpty($tests);
    }

    public function testGetClassesWithNamespaces()
    {
        $this->traverser->setDirectory('tests');
        $this->traverser->setNamespace('PHPMetro\Tests');

        $tests = $this->traverser->getClasses();
        $dir = __DIR__ . '/AnalysesTraverserTest.php';

        $this->assertIsArray($tests);
        $this->assertNotEmpty($tests);

        $this->assertArrayHasKey($dir, $tests);
        $this->assertEquals($tests[$dir], '\PHPMetro\Tests\Service\AnalysesTraverserTest');
    }
}
