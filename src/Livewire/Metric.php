<?php

namespace JeffersonGoncalves\LivewireHorizon\Livewire;

use Illuminate\Contracts\View\View;
use Laravel\Horizon\Contracts\MetricsRepository;
use Livewire\Attributes\Locked;

class Metric extends Page
{
    #[Locked]
    public string $type;

    #[Locked]
    public string $id;

    public function mount(string $type, string $id): void
    {
        $this->type = $type;
        $this->id = $id;
    }

    public function render(): View
    {
        $metrics = app(MetricsRepository::class);

        $snapshots = collect($this->type === 'jobs' ? $metrics->snapshotsForJob($this->id) : $metrics->snapshotsForQueue($this->id))
            ->map(fn (object $snapshot) => [
                'time' => date('H:i', (int) $snapshot->time),
                'throughput' => (int) $snapshot->throughput,
                'runtime' => round($snapshot->runtime / 1000, 3),
            ])
            ->values()
            ->all();

        return $this->page('metric', $this->id, ['snapshots' => $snapshots]);
    }
}
