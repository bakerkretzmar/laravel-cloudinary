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
        config(['laravel-cloudinary.cloud_name' => 'cloud_name']);
        config(['laravel-cloudinary.key' => 'key']);
        config(['laravel-cloudinary.secret' => 'secret']);
        config(['laravel-cloudinary.scaling' => []]);
    }
}
