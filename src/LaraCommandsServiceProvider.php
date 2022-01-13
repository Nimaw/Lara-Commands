<?php

namespace Nimaw\LaraCommands;

use Illuminate\Support\ServiceProvider;
use Nimaw\LaraCommands\Commands\{
    ServiceMakeCommand,
    TraitMakeCommand,
    ViewMakeCommand
};

class LaraCommandsServiceProvider extends ServiceProvider
{

    private static $commands = [
        ViewMakeCommand::class,
        TraitMakeCommand::class,
        ServiceMakeCommand::class,
    ];

    public function register()
    {
        $this->commands(self::$commands);
    }
}
