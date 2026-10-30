<?php

use Illuminate\Support\Facades\Bus;
use JeffersonGoncalves\LivewireHorizon\Jobs\ProcessFailedJobs;
use JeffersonGoncalves\LivewireHorizon\Livewire\Dashboard\Stats;
use JeffersonGoncalves\LivewireHorizon\Livewire\Dashboard\Supervisors;
use JeffersonGoncalves\LivewireHorizon\Livewire\Dashboard\Workload;
use JeffersonGoncalves\LivewireHorizon\Livewire\FailedJobs;
use JeffersonGoncalves\LivewireHorizon\Livewire\Jobs;
use JeffersonGoncalves\LivewireHorizon\Support\JobQuery;
use Laravel\Horizon\Contracts\HorizonCommandQueue;
use Laravel\Horizon\Contracts\JobRepository;
use Laravel\Horizon\Contracts\MasterSupervisorRepository;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\Jobs\RetryFailedJob;
use Laravel\Horizon\SupervisorCommands\Pause;
use Livewire\Livewire;

it('renders every page', function (string $uri) {
    $this->get('livewire-horizon'.$uri)->assertOk();
})->with([
    '',
    '/monitoring',
    '/metrics',
    '/batches',
    '/jobs/pending',
    '/jobs/completed',
    '/jobs/silenced',
    '/failed',
]);

it('uses the Horizon gate', function () {
    Horizon::auth(fn () => false);

    $this->get('livewire-horizon')->assertForbidden();
});

it('searches completed jobs across every stored chunk', function () {
    $jobs = $this->horizonMock(JobRepository::class);
    $jobs->shouldReceive('countCompleted')->andReturn(120);
    $jobs->shouldReceive('getCompleted')->with('-1')->andReturn(collect(range(1, 50))->map(fn ($i) => horizonJob("job-{$i}")));
    $jobs->shouldReceive('getCompleted')->with('49')->andReturn(collect(range(51, 100))->map(fn ($i) => horizonJob("job-{$i}")));
    $jobs->shouldReceive('getCompleted')->with('99')->andReturn(collect([
        horizonJob('job-101', 'App\\Jobs\\ImportOrders', 'imports'),
        horizonJob('job-102'),
    ]));

    Livewire::test(Jobs::class, ['type' => 'completed'])
        ->set('search', 'importorders')
        ->assertViewHas('jobs', fn ($paginator) => $paginator->pluck('id')->all() === ['job-101'])
        ->set('search', 'CUSTOMER-job-77')
        ->assertViewHas('jobs', fn ($paginator) => $paginator->total() === 1 && $paginator->first()->id === 'job-77')
        ->set('search', '')
        ->set('queue', 'imports')
        ->assertViewHas('jobs', fn ($paginator) => $paginator->pluck('id')->all() === ['job-101']);
});

it('retries the selected failed jobs', function () {
    Bus::fake();

    Livewire::test(FailedJobs::class)
        ->set('selected', ['a', 'b'])
        ->call('retrySelected')
        ->assertSet('selected', []);

    Bus::assertDispatchedTimes(RetryFailedJob::class, 2);
});

it('deletes only the failed jobs matching the filter', function () {
    $jobs = $this->horizonMock(JobRepository::class);
    $jobs->shouldReceive('countFailed')->andReturn(3);
    $jobs->shouldReceive('getFailed')->with('-1')->andReturn(collect([
        horizonJob('a', queue: 'mail', attributes: ['status' => 'failed']),
        horizonJob('b', queue: 'imports', attributes: ['status' => 'failed']),
        horizonJob('c', queue: 'mail', attributes: ['status' => 'failed']),
    ]));
    $jobs->shouldReceive('deleteFailed')->with('a')->once();
    $jobs->shouldReceive('deleteFailed')->with('c')->once();
    $jobs->shouldNotReceive('deleteFailed')->with('b');

    (new ProcessFailedJobs('forget', filterQueue: 'mail'))->handle(app(JobQuery::class));
});

it('pauses a master supervisor through the command queue', function () {
    $this->horizonMock(MasterSupervisorRepository::class)
        ->shouldReceive('find')->with('web-1')->andReturn((object) ['name' => 'web-1']);

    $this->horizonMock(HorizonCommandQueue::class)
        ->shouldReceive('push')->with('master:web-1', Pause::class)->once();

    Livewire::test(Supervisors::class)->call('control', 'pause', 'master', 'web-1');
});

it('refreshes each dashboard section on its own', function (string $component) {
    Livewire::test($component)->assertSeeHtml('this.$wire.autoLoad()')->call('autoLoad')->assertOk();
})->with([Stats::class, Workload::class, Supervisors::class]);

it('refuses unknown supervisor commands', function () {
    Livewire::test(Supervisors::class)->call('control', 'terminate', 'master', 'web-1')->assertStatus(422);
});
