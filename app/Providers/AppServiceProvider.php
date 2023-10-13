<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Schema;
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
        // Set the default string length and collation
        Schema::defaultStringLength(191);
        Schema::defaultStringLength(191, 'utf8mb4_unicode_ci');
        $this->app->bind('UserController', function () {
            return new UserController();
        });
    }
}
