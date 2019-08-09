<?php

namespace Bakerkretzmar\LaravelCloudinary\Tests;

use Bakerkretzmar\LaravelCloudinary\LaravelCloudinaryServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [LaravelCloudinaryServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return ['Cloudinaire' => 'Bakerkretzmar\LaravelCloudinary\Facades\Cloudinaire'];
    }

    protected function getEnvironmentSetUp($app)
    {
        if (empty(getenv('CI'))) {
            \Dotenv\Dotenv::create(__DIR__ . '/..', '.env.testing')->load();
        }

        config(['laravel-cloudinary.cloud_name' => getenv('CLOUDINARY_CLOUD_NAME')]);
        config(['laravel-cloudinary.key' => getenv('CLOUDINARY_API_KEY')]);
        config(['laravel-cloudinary.secret' => getenv('CLOUDINARY_API_SECRET')]);
    }
}
