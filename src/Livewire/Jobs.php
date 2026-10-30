<?php

namespace JeffersonGoncalves\LivewireHorizon\Livewire;

use Illuminate\Contracts\View\View;
use JeffersonGoncalves\LivewireHorizon\Support\JobQuery;
use JeffersonGoncalves\LivewireHorizon\Support\Queues;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class Jobs extends Page
{
    use WithPagination;

    #[Locked]
    public string $type = 'pending';

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $queue = '';

    public function mount(string $type): void
    {
        $this->type = $type;
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'queue'], true)) {
            $this->resetPage();
        }
    }

    public function clearQueue(): void
    {
        abort_unless($this->type === 'pending' && $this->queue !== '', 422);

        $count = Queues::clear($this->queue);

        $this->toast("Cleared {$count} jobs from the [{$this->queue}] queue.");
    }

    public function render(): View
    {
        return $this->page('jobs', ucfirst($this->type).' Jobs', [
            'jobs' => app(JobQuery::class)->paginate($this->type, $this->getPage(), trim($this->search), $this->queue),
            'queues' => Queues::names(),
        ]);
    }
}
