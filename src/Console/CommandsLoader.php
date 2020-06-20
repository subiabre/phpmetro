<?php

namespace PHPMetro\Console;

/**
 * Loads the available commands to PHPMetro
 * @package PHPMetro
 * @author subiabrewd@gmail.com
 */
class CommandsLoader
{
    public function getCommands(): array
    {
        return [
            // Commands instances
            new Run
        ];
    }
}
