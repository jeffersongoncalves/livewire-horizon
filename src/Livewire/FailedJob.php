<?php

namespace JeffersonGoncalves\LivewireHorizon\Livewire;

use Illuminate\Contracts\View\View;
use JeffersonGoncalves\LivewireHorizon\Support\FailedJobActions;
use Laravel\Horizon\Http\Controllers\FailedJobsController;
use Livewire\Attributes\Locked;

class FailedJob extends Page
{
    #[Locked]
    public string $id;

    public function mount(string $id): void
    {
        $this->id = $id;
    }

    public function retry(): void
    {
        FailedJobActions::retry([$this->id]);

        $this->toast('Job queued for retry.');
    }

    public function forget(): void
    {
        FailedJobActions::forget([$this->id]);

        $this->toast('Failed job deleted.');
        $this->redirectRoute('livewire-horizon.failed', navigate: true);
    }

    public function render(): View
    {
        $job = app(FailedJobsController::class)->show($this->id);

        abort_if($job === [], 404);

        return $this->page('failed-job', 'Failed Job', ['job' => (object) $job]);
    }
}
