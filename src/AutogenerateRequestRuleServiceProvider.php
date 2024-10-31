<?php

namespace Ryanroydev\AutogenerateRequestRule;

use Illuminate\Support\ServiceProvider;

class AutogenerateRequestRuleServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Load routes, views, migrations, etc.
        $this->mergeConfigFrom(__DIR__.'/config/config.php', 'AutogenerateRequestRule');
     
    }

    public function register()
    {
        // Register any package services
        $this->commands([
            Commands\AutogenerateRequestRule::class,
        ]);
    }
}
