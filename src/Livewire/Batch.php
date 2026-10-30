<?php

namespace JeffersonGoncalves\LivewireHorizon\Livewire;

use Illuminate\Contracts\View\View;
use JeffersonGoncalves\LivewireHorizon\Support\JobQuery;
use Laravel\Horizon\Http\Controllers\BatchesController;
use Livewire\Attributes\Locked;

class Batch extends Page
{
    #[Locked]
    public string $id;

    public function mount(string $id): void
    {
        $this->id = $id;
    }

    public function retry(): void
    {
        app(BatchesController::class)->retry($this->id);

        $this->toast('Failed jobs of this batch queued for retry.');
    }

    public function render(): View
    {
        $data = app(BatchesController::class)->show($this->id);

        abort_if($data['batch'] === null, 404);

        return $this->page('batch', $data['batch']->name ?: $this->id, [
            'batch' => $data['batch'],
            'failedJobs' => collect($data['failedJobs'])->map(JobQuery::decode(...)),
        ]);
    }
}
