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
            __DIR__ . '/Config/entityregister.php' => config_path('entityregister.php'),
        ]);

        $router = $this->app["router"];
        $router->pushMiddlewareToGroup('web', BindEntity::class);
        $router->pushMiddlewareToGroup('api', BindEntity::class);
    }

}
