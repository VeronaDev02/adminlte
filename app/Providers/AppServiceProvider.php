<?php

namespace App\Providers;

use App\Observers\SelfsObserver;
use App\Models\Selfs;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Selfs::observe(SelfsObserver::class);
    }
}
