<?php

namespace JeffersonGoncalves\LivewireHorizon\Tests;

use JeffersonGoncalves\LivewireHorizon\LivewireHorizonServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            LivewireHorizonServiceProvider::class,
        ];
    }
}
