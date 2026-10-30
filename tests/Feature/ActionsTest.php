<?php

use Carbon\CarbonImmutable;
use Illuminate\Bus\Batch as BusBatch;
use Illuminate\Bus\BatchRepository;
use Illuminate\Contracts\Queue\Factory as QueueFactory;
use Illuminate\Support\Facades\Bus;
use JeffersonGoncalves\LivewireHorizon\Jobs\ProcessFailedJobs;
use JeffersonGoncalves\LivewireHorizon\Livewire\Batch;
use JeffersonGoncalves\LivewireHorizon\Livewire\Dashboard;
use JeffersonGoncalves\LivewireHorizon\Livewire\FailedJob;
use JeffersonGoncalves\LivewireHorizon\Livewire\FailedJobs;
use JeffersonGoncalves\LivewireHorizon\Livewire\Jobs;
use JeffersonGoncalves\LivewireHorizon\Livewire\Monitoring;
use JeffersonGoncalves\LivewireHorizon\Support\JobQuery;
use JeffersonGoncalves\LivewireHorizon\Support\Queues;
use Laravel\Horizon\Contracts\JobRepository;
use Laravel\Horizon\Http\Controllers\BatchesController;
use Laravel\Horizon\Http\Controllers\FailedJobsController;
use Laravel\Horizon\Jobs\RetryFailedJob;
use Laravel\Horizon\Jobs\StopMonitoringTag;
use Livewire\Livewire;

function fakeQueueConnection(string $connection, string $queue, int $cleared): void
{
    $driver = Mockery::mock();
    $driver->shouldReceive('clear')->with($queue)->once()->andReturn($cleared);

    $manager = Mockery::mock(app('queue'))->makePartial();
    $manager->shouldReceive('connection')->with($connection)->andReturn($driver);

    app()->instance('queue', $manager);
}

it('runs retry all and delete all on the queue with the current filter', function () {
    Bus::fake();

    Livewire::test(FailedJobs::class)
        ->set('search', ' invoice ')
        ->set('queue', 'mail')
        ->call('retryAll')
        ->call('forgetAll')
        ->assertDispatched('toast-show');

    Bus::assertDispatched(ProcessFailedJobs::class, fn ($job) => $job->action === 'retry' && $job->search === 'invoice' && $job->filterQueue === 'mail');
    Bus::assertDispatched(ProcessFailedJobs::class, fn ($job) => $job->action === 'forget' && $job->filterQueue === 'mail');
});

it('retries every stored failed job when unfiltered, past the search limit', function () {
    Bus::fake([RetryFailedJob::class]);
    config(['livewire-horizon.search_limit' => 50]);

    $jobs = $this->horizonMock(JobRepository::class);
    $jobs->shouldReceive('countFailed')->andReturn(70);
    $jobs->shouldReceive('getFailed')->with('-1')->andReturn(collect(range(1, 50))->map(fn ($i) => horizonJob("f-{$i}")));
    $jobs->shouldReceive('getFailed')->with('49')->andReturn(collect(range(51, 70))->map(fn ($i) => horizonJob("f-{$i}")));

    (new ProcessFailedJobs('retry'))->handle(app(JobQuery::class));

    Bus::assertDispatchedTimes(RetryFailedJob::class, 70);
});

it('still caps a filtered scan at the search limit', function () {
    config(['livewire-horizon.search_limit' => 50]);

    $jobs = $this->horizonMock(JobRepository::class);
    $jobs->shouldReceive('countFailed')->andReturn(70);
    $jobs->shouldReceive('getFailed')->with('-1')->andReturn(collect(range(1, 50))->map(fn ($i) => horizonJob("f-{$i}")));
    $jobs->shouldNotReceive('getFailed')->with('49');

    expect(app(JobQuery::class)->matching('failed', 'f-'))->toHaveCount(50);
});

it('clears a queue on the connection of the supervisor working it', function () {
    config([
        'horizon.defaults' => ['supervisor-1' => ['connection' => 'redis', 'queue' => ['default']]],
        'horizon.environments' => ['testing' => ['supervisor-2' => ['connection' => 'redis-imports', 'queue' => 'imports,exports']]],
    ]);

    expect(Queues::connection('exports'))->toBe('redis-imports')
        ->and(Queues::connection('default'))->toBe('redis')
        ->and(Queues::connection('unknown'))->toBe('redis');

    fakeQueueConnection('redis-imports', 'imports', 7);

    Livewire::test(Dashboard::class)->call('clearQueue', 'imports')->assertDispatched('toast-show');
});

it('clears the filtered queue from the pending jobs page', function () {
    fakeQueueConnection('redis', 'mail', 3);

    Livewire::test(Jobs::class, ['type' => 'pending'])->set('queue', 'mail')->call('clearQueue')->assertOk();
});

it('refuses to clear a combined workload row', function () {
    Livewire::test(Dashboard::class)->call('clearQueue', 'default,mail')->assertStatus(422);
});

it('stops monitoring a tag', function () {
    Bus::fake();

    Livewire::test(Monitoring::class)->call('stopMonitoring', 'App\\Models\\User:1');

    Bus::assertDispatched(StopMonitoringTag::class, fn ($job) => $job->tag === 'App\\Models\\User:1');
});

it('retries the failed jobs of a batch', function () {
    $batch = new BusBatch(app(QueueFactory::class), Mockery::mock(BatchRepository::class), 'batch-1', 'Import', 3, 1, 1, ['f-1'], [], CarbonImmutable::now());

    $controller = Mockery::mock(BatchesController::class);
    $controller->shouldReceive('show')->with('batch-1')->andReturn(['batch' => $batch, 'failedJobs' => []]);
    $controller->shouldReceive('retry')->with('batch-1')->once();
    app()->instance(BatchesController::class, $controller);

    Livewire::test(Batch::class, ['id' => 'batch-1'])->call('retry')->assertDispatched('toast-show');
});

it('toasts after deleting a failed job', function () {
    $controller = Mockery::mock(FailedJobsController::class);
    $controller->shouldReceive('show')->andReturn((array) horizonJob('f-1', attributes: ['status' => 'failed', 'payload' => json_decode(horizonJob('f-1')->payload), 'retried_by' => []]));
    app()->instance(FailedJobsController::class, $controller);

    $this->horizonMock(JobRepository::class)->shouldReceive('deleteFailed')->with('f-1')->once();

    Livewire::test(FailedJob::class, ['id' => 'f-1'])
        ->call('forget')
        ->assertDispatched('toast-show')
        ->assertRedirect(route('livewire-horizon.failed'));
});

it('renders the shared confirmation modal and the loading bar', function () {
    $this->get('livewire-horizon/failed')
        ->assertOk()
        ->assertSee('horizon-confirm', false)
        ->assertSee('animate-horizon-progress', false);
});
