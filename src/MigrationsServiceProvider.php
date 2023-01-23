<?php

namespace Eduka\Migrations;

use Eduka\Abstracts\Classes\EdukaServiceProvider;

class MigrationsServiceProvider extends EdukaServiceProvider
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
