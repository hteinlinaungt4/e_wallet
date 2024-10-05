<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades;
use Illuminate\View\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();
        Facades\View::composer('*', function (View $view) {
            $unreadnoti = 0;


            if(auth()->guard('web')->check()){
                $unreadnoti = auth()->guard('web')->user()->unreadNotifications()->count();
            }
            $view->with('noticount', $unreadnoti);
        });


    }
}
