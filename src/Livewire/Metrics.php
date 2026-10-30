<?php

namespace JeffersonGoncalves\LivewireHorizon\Livewire;

use Illuminate\Contracts\View\View;
use Laravel\Horizon\Contracts\MetricsRepository;
use Livewire\Attributes\Url;

class Metrics extends Page
{
    #[Url(except: 'jobs')]
    public string $type = 'jobs';

    public function render(): View
    {
        $metrics = app(MetricsRepository::class);
        $jobs = $this->type === 'jobs';

        $items = collect($jobs ? $metrics->measuredJobs() : $metrics->measuredQueues())
            ->sort()
            ->map(fn (string $name) => (object) [
                'name' => $name,
                'throughput' => $jobs ? $metrics->throughputForJob($name) : $metrics->throughputForQueue($name),
                'runtime' => $jobs ? $metrics->runtimeForJob($name) : $metrics->runtimeForQueue($name),
            ])
            ->values();

        return $this->page('metrics', 'Metrics', ['items' => $items]);
    }
}
