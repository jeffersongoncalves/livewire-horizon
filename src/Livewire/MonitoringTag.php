<?php

namespace JeffersonGoncalves\LivewireHorizon\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use JeffersonGoncalves\LivewireHorizon\Support\JobQuery;
use Laravel\Horizon\Contracts\JobRepository;
use Laravel\Horizon\Contracts\TagRepository;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class MonitoringTag extends Page
{
    use WithPagination;

    protected const PER_PAGE = 25;

    #[Locked]
    public string $tag;

    #[Url(except: 'jobs')]
    public string $tab = 'jobs';

    public function mount(string $tag): void
    {
        $this->tag = $tag;
    }

    public function updatedTab(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $key = $this->tab === 'failed' ? "failed:{$this->tag}" : $this->tag;
        $tags = app(TagRepository::class);
        $page = $this->getPage();
        $startingAt = ($page - 1) * self::PER_PAGE;

        $jobs = app(JobRepository::class)
            ->getJobs($tags->paginate($key, $startingAt, self::PER_PAGE), $startingAt)
            ->map(JobQuery::decode(...))
            ->values();

        return $this->page('monitoring-tag', $this->tag, [
            'jobs' => new LengthAwarePaginator($jobs, $tags->count($key), self::PER_PAGE, $page),
        ]);
    }
}
