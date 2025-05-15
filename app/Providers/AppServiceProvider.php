<?php

namespace App\Providers;

use App\Http\Livewire\Admin\Unidades\ToggleApiStatus;
use Livewire\Livewire;
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
        Livewire::component('admin.unidades.toggle-api-status', ToggleApiStatus::class);
    }
}
