<?php

namespace Doctrine\BindEntity;

use Illuminate\Support\ServiceProvider;
use Doctrine\BindEntity\Http\Middleware\BindEntity;

class BindEntityServiceProvider extends ServiceProvider
{

    public function register()
    {

    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/Config/bindentityconfig.php' => config_path('bindentityconfig.php'),
        ]);
    }

}
