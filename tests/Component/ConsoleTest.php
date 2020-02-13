<?php

namespace PHPMetro\Tests\Component;

use PHPUnit\Framework\TestCase;
use PHPMetro\Component\Console;

class ConsoleTest extends TestCase
{
    public $console;

    public function setUp(): void
    {
        $this->console = new Console;
    }

    public function testConsoleTakesInputFromCLI()
    {
        $input = $this->console->read('Please say something for the test:');

        $this->assertEquals(
            $input,
            $this->input
        );
    }

    public function testConsoleTransformsArrayToString()
    {
        $output = $this->console->arrayToString([
            'KEY' => 'value'
        ]);

        $this->assertEquals(
            "KEY: value",
            $output
        );
    }
}
