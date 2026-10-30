<?php

namespace JeffersonGoncalves\LivewireHorizon;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LivewireHorizonServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('livewire-horizon')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigrations();
    }
}
