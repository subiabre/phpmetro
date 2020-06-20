<?php

namespace PHPMetro\Tests\Console;

use PHPMetro\Console\CommandsLoader;
use PHPUnit\Framework\TestCase;

class CommandsLoaderTest extends TestCase
{
    public function testReturnsArray()
    {
        $commands = (new CommandsLoader)->getCommands();

        $this->assertIsArray($commands);
    }
}
