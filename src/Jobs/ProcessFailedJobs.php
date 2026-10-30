<?php

namespace JeffersonGoncalves\LivewireHorizon\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use JeffersonGoncalves\LivewireHorizon\Support\FailedJobActions;
use JeffersonGoncalves\LivewireHorizon\Support\JobQuery;

/**
 * "Retry all" / "Delete all" can touch thousands of jobs: run them on the queue,
 * not inside the Livewire request.
 */
class ProcessFailedJobs implements ShouldQueue
{
    use Queueable;

    /**
     * @param  'retry'|'forget'  $action
     */
    public function __construct(
        public string $action,
        public string $search = '',
        // Not $queue: Queueable uses that property to pick where this job runs.
        public string $filterQueue = '',
    ) {}

    public function handle(JobQuery $query): void
    {
        $ids = $query->matching('failed', $this->search, $this->filterQueue)->pluck('id')->all();

        match ($this->action) {
            'retry' => FailedJobActions::retry($ids),
            'forget' => FailedJobActions::forget($ids),
        };
    }
}
