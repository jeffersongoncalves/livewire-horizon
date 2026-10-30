<?php

namespace JeffersonGoncalves\LivewireHorizon\Livewire;

use Illuminate\Contracts\View\View;
use JeffersonGoncalves\LivewireHorizon\Support\FailedJobActions;
use JeffersonGoncalves\LivewireHorizon\Support\JobQuery;
use JeffersonGoncalves\LivewireHorizon\Support\Queues;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class FailedJobs extends Page
{
    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $queue = '';

    /** @var array<int, string> */
    public array $selected = [];

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'queue'], true)) {
            $this->resetPage();
            $this->selected = [];
        }
    }

    public function retry(string $id): void
    {
        FailedJobActions::retry([$id]);

        $this->toast('Job queued for retry.');
    }

    public function retrySelected(): void
    {
        $count = FailedJobActions::retry($this->selected);
        $this->selected = [];

        $this->toast("{$count} jobs queued for retry.");
    }

    public function forgetSelected(): void
    {
        $count = FailedJobActions::forget($this->selected);
        $this->selected = [];

        $this->toast("{$count} failed jobs deleted.");
    }

    /**
     * Retry every failed job matching the current search and queue filter.
     */
    public function retryAll(): void
    {
        $count = FailedJobActions::retry($this->matchingIds());

        $this->toast("{$count} jobs queued for retry.");
    }

    /**
     * Delete every failed job matching the current search and queue filter.
     */
    public function forgetAll(): void
    {
        $count = FailedJobActions::forget($this->matchingIds());
        $this->selected = [];
        $this->resetPage();

        $this->toast("{$count} failed jobs deleted.");
    }

    /**
     * @return array<int, string>
     */
    protected function matchingIds(): array
    {
        return app(JobQuery::class)
            ->matching('failed', trim($this->search), $this->queue)
            ->pluck('id')
            ->all();
    }

    public function render(): View
    {
        return $this->page('failed-jobs', 'Failed Jobs', [
            'jobs' => app(JobQuery::class)->paginate('failed', $this->getPage(), trim($this->search), $this->queue),
            'queues' => Queues::names(),
        ]);
    }
}
