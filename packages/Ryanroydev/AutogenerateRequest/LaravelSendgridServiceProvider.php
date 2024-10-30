<?php

namespace Ryanroydev\LaravelSendgrid;

use Illuminate\Support\ServiceProvider;

class LaravelSendgridServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Load routes, views, migrations, etc.
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->mergeConfigFrom(__DIR__.'/config/config.php', 'yourpackagename');
    }

    public function register()
    {
        // Register any package services
    }
}
