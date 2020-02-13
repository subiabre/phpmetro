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
        $this->console->write('Please say something for the test: ');
        $input = $this->console->read();

        $this->assertEquals(
            $input,
            $this->console->input
        );
    }

    public function testConsoleTransformsArrayToString()
    {
        $output = $this->console->arrayToString([
            'KEY' => 'value'
        ]);

        $this->assertEquals(
            "KEY: value\n",
            $output
        );
    }
}
