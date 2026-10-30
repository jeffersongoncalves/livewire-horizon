<?php

use Laravel\Horizon\Contracts\JobRepository;
use Laravel\Horizon\Contracts\MasterSupervisorRepository;
use Laravel\Horizon\Contracts\MetricsRepository;
use Laravel\Horizon\Contracts\SupervisorRepository;
use Laravel\Horizon\Contracts\TagRepository;
use Laravel\Horizon\Contracts\WorkloadRepository;

beforeEach(function () {
    $failed = horizonJob('failed-1', attributes: [
        'status' => 'failed',
        'failed_at' => 1700000002,
        'exception' => 'RuntimeException: Mail server down',
        'context' => json_encode(['invoice' => 42]),
        'retried_by' => json_encode([['id' => 'retry-1', 'status' => 'completed', 'retried_at' => 1700000100]]),
        'payload' => json_encode(['displayName' => 'App\\Jobs\\SendInvoice', 'tags' => ['App\\Models\\Invoice:42'], 'pushedAt' => 1700000000, 'attempts' => 3]),
    ]);

    $jobs = $this->horizonMock(JobRepository::class);
    $jobs->shouldReceive('countFailed')->andReturn(1);
    $jobs->shouldReceive('getFailed')->andReturn(collect([$failed]));
    $jobs->shouldReceive('countCompleted')->andReturn(1);
    $jobs->shouldReceive('getCompleted')->andReturn(collect([horizonJob('done-1')]));
    $jobs->shouldReceive('getJobs')->with(['failed-1'])->andReturn(collect([clone $failed]));
    $jobs->shouldReceive('getJobs')->with(['done-1'])->andReturn(collect([horizonJob('done-1')]));
    $jobs->shouldReceive('getJobs')->andReturn(collect([horizonJob('done-1')]));

    $this->horizonMock(MasterSupervisorRepository::class)->shouldReceive('all')->andReturn([
        (object) ['name' => 'web-1', 'environment' => null, 'pid' => 1, 'status' => 'running'],
    ]);
    $this->horizonMock(SupervisorRepository::class)->shouldReceive('all')->andReturn([
        (object) ['name' => 'web-1:supervisor-1', 'master' => 'web-1', 'pid' => 2, 'status' => 'paused', 'processes' => ['redis:default' => 3], 'options' => ['queue' => 'default,mail', 'balance' => 'auto']],
    ]);
    $this->horizonMock(WorkloadRepository::class)->shouldReceive('get')->andReturn([
        ['name' => 'default', 'length' => 12, 'wait' => 90, 'processes' => 3, 'split_queues' => null],
    ]);
    $this->horizonMock(TagRepository::class)->shouldReceive('monitoring')->andReturn(['App\\Models\\Invoice:42']);
    $this->horizonMock(TagRepository::class)->shouldReceive('count')->andReturn(1);
    $this->horizonMock(TagRepository::class)->shouldReceive('paginate')->andReturn(['done-1']);

    $metrics = $this->horizonMock(MetricsRepository::class);
    $metrics->shouldReceive('measuredJobs')->andReturn(['App\\Jobs\\SendInvoice']);
    $metrics->shouldReceive('snapshotsForJob')->andReturn([(object) ['throughput' => 10, 'runtime' => 1500, 'time' => 1700000000]]);
});

it('renders pages with data', function (string $uri, string $text) {
    $this->get('livewire-horizon'.$uri)->assertOk()->assertSee($text, false);
})->with([
    'dashboard' => ['', 'supervisor-1'],
    'workload' => ['', 'Clear'],
    'completed' => ['/jobs/completed', 'done-1'],
    'failed' => ['/failed', 'SendInvoice'],
    'failed job' => ['/failed/failed-1', 'Mail server down'],
    'retries' => ['/failed/failed-1', 'retry-1'],
    'job' => ['/job/done-1', 'customer-done-1'],
    'monitoring' => ['/monitoring', 'Invoice:42'],
    'monitoring tag' => ['/monitoring/'.rawurlencode('App\\Models\\Invoice:42'), 'done-1'],
    'metrics' => ['/metrics', 'SendInvoice'],
    'metric' => ['/metrics/jobs/'.rawurlencode('App\\Jobs\\SendInvoice'), 'Throughput'],
]);
