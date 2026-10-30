<?php

namespace JeffersonGoncalves\LivewireHorizon;

use JeffersonGoncalves\LivewireHorizon\Livewire\Batch;
use JeffersonGoncalves\LivewireHorizon\Livewire\Batches;
use JeffersonGoncalves\LivewireHorizon\Livewire\Dashboard;
use JeffersonGoncalves\LivewireHorizon\Livewire\FailedJob;
use JeffersonGoncalves\LivewireHorizon\Livewire\FailedJobs;
use JeffersonGoncalves\LivewireHorizon\Livewire\Job;
use JeffersonGoncalves\LivewireHorizon\Livewire\Jobs;
use JeffersonGoncalves\LivewireHorizon\Livewire\Metric;
use JeffersonGoncalves\LivewireHorizon\Livewire\Metrics;
use JeffersonGoncalves\LivewireHorizon\Livewire\Monitoring;
use JeffersonGoncalves\LivewireHorizon\Livewire\MonitoringTag;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LivewireHorizonServiceProvider extends PackageServiceProvider
{
    /** @var array<string, class-string> */
    public const COMPONENTS = [
        'dashboard' => Dashboard::class,
        'dashboard.stats' => Dashboard\Stats::class,
        'dashboard.workload' => Dashboard\Workload::class,
        'dashboard.supervisors' => Dashboard\Supervisors::class,
        'monitoring' => Monitoring::class,
        'monitoring-tag' => MonitoringTag::class,
        'metrics' => Metrics::class,
        'metric' => Metric::class,
        'batches' => Batches::class,
        'batch' => Batch::class,
        'jobs' => Jobs::class,
        'job' => Job::class,
        'failed-jobs' => FailedJobs::class,
        'failed-job' => FailedJob::class,
    ];

    public function configurePackage(Package $package): void
    {
        $package
            ->name('livewire-horizon')
            ->hasConfigFile()
            ->hasViews()
            ->hasRoute('web');
    }

    public function packageBooted(): void
    {
        foreach (self::COMPONENTS as $name => $class) {
            Livewire::component("livewire-horizon.{$name}", $class);
        }
    }
}
