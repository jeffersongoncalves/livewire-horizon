<?php

namespace JeffersonGoncalves\LivewireHorizon\Livewire;

use Illuminate\Contracts\View\View;
use JeffersonGoncalves\LivewireHorizon\Support\JobQuery;
use Laravel\Horizon\Contracts\JobRepository;
use Livewire\Attributes\Locked;

class Job extends Page
{
    #[Locked]
    public string $id;

    public function mount(string $id): void
    {
        $this->id = $id;
    }

    public function render(): View
    {
        $job = app(JobRepository::class)->getJobs([$this->id])->first();

        abort_if($job === null, 404);

        return $this->page('job', 'Job', ['job' => JobQuery::decode($job)]);
    }
}
