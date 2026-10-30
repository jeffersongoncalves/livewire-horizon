<?php

namespace JeffersonGoncalves\LivewireHorizon\Livewire\Dashboard;

use Illuminate\Contracts\View\View;
use JeffersonGoncalves\LivewireHorizon\Livewire\Page;
use Laravel\Horizon\Http\Controllers\DashboardStatsController;

class Stats extends Page
{
    public function render(): View
    {
        return $this->page('dashboard.stats', 'Dashboard', [
            'stats' => app(DashboardStatsController::class)->index(),
        ]);
    }
}
