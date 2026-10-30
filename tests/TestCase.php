<?php

namespace JeffersonGoncalves\LivewireHorizon\Tests;

use Flux\FluxServiceProvider;
use FluxPro\FluxProServiceProvider;
use JeffersonGoncalves\LivewireHorizon\LivewireHorizonServiceProvider;
use Laravel\Horizon\Contracts\HorizonCommandQueue;
use Laravel\Horizon\Contracts\JobRepository;
use Laravel\Horizon\Contracts\MasterSupervisorRepository;
use Laravel\Horizon\Contracts\MetricsRepository;
use Laravel\Horizon\Contracts\SupervisorRepository;
use Laravel\Horizon\Contracts\TagRepository;
use Laravel\Horizon\Contracts\WorkloadRepository;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonServiceProvider;
use Laravel\Horizon\WaitTimeCalculator;
use Livewire\LivewireServiceProvider;
use Mockery;
use Mockery\MockInterface;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Horizon::auth(fn () => true);

        $this->fakeHorizon();
    }

    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            FluxServiceProvider::class,
            FluxProServiceProvider::class,
            HorizonServiceProvider::class,
            LivewireHorizonServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(str_repeat('a', 32)));
    }

    /**
     * Replace Horizon's Redis repositories with empty mocks; tests override what they need.
     */
    protected function fakeHorizon(): void
    {
        $this->mockContract(JobRepository::class, function (MockInterface $mock) {
            foreach (['countRecent', 'countRecentlyFailed', 'countPending', 'countCompleted', 'countSilenced', 'countFailed'] as $method) {
                $mock->shouldReceive($method)->andReturn(0)->byDefault();
            }
            foreach (['getPending', 'getCompleted', 'getSilenced', 'getFailed', 'getJobs'] as $method) {
                $mock->shouldReceive($method)->andReturn(collect())->byDefault();
            }
        });

        $this->mockContract(MetricsRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('jobsProcessedPerMinute', 'throughputForJob', 'throughputForQueue', 'runtimeForJob', 'runtimeForQueue')->andReturn(0)->byDefault();
            $mock->shouldReceive('queueWithMaximumRuntime', 'queueWithMaximumThroughput')->andReturn(null)->byDefault();
            $mock->shouldReceive('measuredJobs', 'measuredQueues', 'snapshotsForJob', 'snapshotsForQueue')->andReturn([])->byDefault();
        });

        $this->mockContract(MasterSupervisorRepository::class, fn (MockInterface $mock) => $mock->shouldReceive('all')->andReturn([])->byDefault());
        $this->mockContract(SupervisorRepository::class, fn (MockInterface $mock) => $mock->shouldReceive('all')->andReturn([])->byDefault());
        $this->mockContract(WorkloadRepository::class, fn (MockInterface $mock) => $mock->shouldReceive('get')->andReturn([])->byDefault());
        $this->mockContract(TagRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('monitoring')->andReturn([])->byDefault();
            $mock->shouldReceive('count')->andReturn(0)->byDefault();
            $mock->shouldReceive('paginate')->andReturn([])->byDefault();
        });
        $this->mockContract(WaitTimeCalculator::class, fn (MockInterface $mock) => $mock->shouldReceive('calculate')->andReturn([])->byDefault());
        $this->mockContract(HorizonCommandQueue::class, fn () => null);
        $this->mockContract('queue.failer', fn (MockInterface $mock) => $mock->shouldReceive('forget')->andReturn(true)->byDefault(), FailedJobProviderInterface::class);
    }

    /**
     * @param  class-string  $abstract
     */
    protected function mockContract(string $abstract, callable $setup, ?string $type = null): MockInterface
    {
        $mock = Mockery::mock($type ?? $abstract);
        $setup($mock);

        $this->app->instance($abstract, $mock);

        return $mock;
    }

    protected function horizonMock(string $abstract): MockInterface
    {
        return $this->app->make($abstract);
    }
}
