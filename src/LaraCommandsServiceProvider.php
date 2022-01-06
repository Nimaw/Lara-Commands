<?php

namespace Nimaw\LaraCommands;

use Illuminate\Support\ServiceProvider;
use Nimaw\LaraCommands\commands\ViewMakeCommand;

class LaraCommandsServiceProvider extends ServiceProvider
{

    private static $commands = [
        ViewMakeCommand::class
    ];

    public function register()
    {
        $this->commands(self::$commands);
    }
}
