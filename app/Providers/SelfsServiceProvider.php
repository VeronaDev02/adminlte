<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\Selfs\PythonApiServiceInterface;
use App\Services\Selfs\PythonApiService;
use App\Services\Selfs\GridLayoutService;

class SelfsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            PythonApiServiceInterface::class, 
            PythonApiService::class
        );

        $this->app->bind(
            GridLayoutService::class, 
            GridLayoutService::class
        );
    }

    public function boot()
    {
    }
}