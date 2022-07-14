<?php

namespace Eduka\Migrations;

use Illuminate\Support\ServiceProvider;

class EdukaMigrationsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    public function register()
    {
        //
    }
}
