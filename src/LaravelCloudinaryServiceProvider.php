<?php

namespace Bakerkretzmar\LaravelCloudinary;

use Illuminate\Support\ServiceProvider;

class LaravelCloudinaryServiceProvider extends ServiceProvider
{
    protected $defer = false;

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/laravel-cloudinary.php' => config_path('laravel-cloudinary.php'),
        ]);
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravel-cloudinary.php', 'laravel-cloudinary');

        $this->app->singleton(Cloudinaire::class, function () {
            return new Cloudinaire(config('laravel-cloudinary'));
        });

        $this->app->alias(Cloudinaire::class, 'cloudinaire');
    }
}
