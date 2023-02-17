<?php

namespace AmRo045\LaravelHashId\Tests;

use AmRo045\LaravelHashId\PackageServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            PackageServiceProvider::class,
        ];
    }
}