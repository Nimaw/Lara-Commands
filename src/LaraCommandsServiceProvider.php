<?php

namespace Nimaw\LaraCommands;

use Illuminate\Support\ServiceProvider;

class LaraCommandsServiceProvider extends ServiceProvider
{

    protected $commands = [
        //
    ];

    public function register()
    {
        $this->app->bind('LaraCommands', function () {
            return new LaraCommands();
        });
        $this->commands($this->commands);
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/lara-commands.php' => config_path('lara-commands.php')
        ]);
    }
}
