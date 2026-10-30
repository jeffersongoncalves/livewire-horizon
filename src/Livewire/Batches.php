<?php

namespace JeffersonGoncalves\LivewireHorizon\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Laravel\Horizon\Http\Controllers\BatchesController;
use Livewire\Attributes\Url;

class Batches extends Page
{
    #[Url(except: '')]
    public string $search = '';

    /** Cursor: show batches older than this ID. */
    #[Url(except: '')]
    public string $before = '';

    public function updatedSearch(): void
    {
        $this->before = '';
    }

    public function render(): View
    {
        $batches = app(BatchesController::class)->index(new Request(array_filter([
            'query' => trim($this->search),
            'before_id' => $this->before,
        ])))['batches'];

        return $this->page('batches', 'Batches', ['batches' => collect($batches)]);
    }
}
