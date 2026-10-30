<?php

namespace JeffersonGoncalves\LivewireHorizon\Livewire\Dashboard;

use Illuminate\Contracts\View\View;
use JeffersonGoncalves\LivewireHorizon\Livewire\Page;
use JeffersonGoncalves\LivewireHorizon\Support\Queues;
use Laravel\Horizon\Contracts\WorkloadRepository;

class Workload extends Page
{
    public function clearQueue(string $queue): void
    {
        $count = Queues::clear($queue);

        $this->toast("Cleared {$count} jobs from the [{$queue}] queue.");
    }

    public function render(): View
    {
        return $this->page('dashboard.workload', 'Dashboard', [
            'workload' => collect(app(WorkloadRepository::class)->get())->sortBy('name')->values(),
        ]);
    }
}
