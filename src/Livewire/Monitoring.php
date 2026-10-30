<?php

namespace JeffersonGoncalves\LivewireHorizon\Livewire;

use Flux\FluxManager;
use Illuminate\Contracts\View\View;
use Laravel\Horizon\Http\Controllers\MonitoringController;
use Laravel\Horizon\Jobs\MonitorTag;
use Laravel\Horizon\Jobs\StopMonitoringTag;

class Monitoring extends Page
{
    public string $tag = '';

    public function monitor(): void
    {
        $this->validate(['tag' => 'required|string|max:255']);

        dispatch(new MonitorTag(trim($this->tag)));

        $this->reset('tag');
        app(FluxManager::class)->modal('monitor-tag')->close();
    }

    public function stopMonitoring(string $tag): void
    {
        dispatch(new StopMonitoringTag($tag));

        $this->toast("Stopped monitoring [{$tag}].");
    }

    public function render(): View
    {
        return $this->page('monitoring', 'Monitoring', [
            'tags' => app(MonitoringController::class)->index(),
        ]);
    }
}
