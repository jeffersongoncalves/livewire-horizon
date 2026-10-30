<?php

namespace JeffersonGoncalves\LivewireHorizon\Livewire;

use Illuminate\Contracts\View\View;

/**
 * Shell for the overview: stats, workload and supervisors are separate
 * components, each polling on its own.
 */
class Dashboard extends Page
{
    public function render(): View
    {
        return $this->page('dashboard', 'Dashboard');
    }
}
