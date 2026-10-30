<div class="space-y-6">
    <div class="flex flex-wrap items-center gap-2">
        <flux:heading size="xl">Failed Jobs</flux:heading>
        <flux:spacer />

        @if (count($selected) > 0)
            <flux:button size="sm" icon="arrow-path" wire:click="retrySelected">Retry {{ count($selected) }} selected</flux:button>
            <flux:button size="sm" variant="danger" icon="trash" wire:click="forgetSelected"
                wire:confirm="Delete the {{ count($selected) }} selected failed jobs?">Delete selected</flux:button>
        @else
            <flux:button size="sm" icon="arrow-path" wire:click="retryAll" :disabled="$jobs->total() === 0"
                wire:confirm="Retry all {{ number_format($jobs->total()) }} {{ $search !== '' || $queue !== '' ? 'matching ' : '' }}failed jobs?">
                Retry all{{ $search !== '' || $queue !== '' ? ' matching' : '' }}
            </flux:button>
            <flux:button size="sm" variant="danger" icon="trash" wire:click="forgetAll" :disabled="$jobs->total() === 0"
                wire:confirm="Delete all {{ number_format($jobs->total()) }} {{ $search !== '' || $queue !== '' ? 'matching ' : '' }}failed jobs? This cannot be undone.">
                Delete all{{ $search !== '' || $queue !== '' ? ' matching' : '' }}
            </flux:button>
        @endif
    </div>

    <flux:card class="space-y-4">
        @include('livewire-horizon::partials.filters')

        <flux:table :paginate="$jobs">
            <flux:table.columns>
                <flux:table.column class="w-8"></flux:table.column>
                <flux:table.column>Job</flux:table.column>
                <flux:table.column align="end">Runtime</flux:table.column>
                <flux:table.column align="end">Failed</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($jobs as $job)
                    <flux:table.row :key="$job->id">
                        <flux:table.cell><flux:checkbox wire:model.live="selected" :value="$job->id" /></flux:table.cell>
                        <flux:table.cell>@include('livewire-horizon::partials.job-name', ['failed' => true])</flux:table.cell>
                        <flux:table.cell align="end">{{ \JeffersonGoncalves\LivewireHorizon\LivewireHorizon::runtime((object) ['completed_at' => $job->failed_at ?? null, 'reserved_at' => $job->reserved_at ?? null]) ?? '-' }}</flux:table.cell>
                        <flux:table.cell align="end">{{ \JeffersonGoncalves\LivewireHorizon\LivewireHorizon::date($job->failed_at ?? null) }}</flux:table.cell>
                        <flux:table.cell align="end">
                            <flux:button size="xs" variant="subtle" icon="arrow-path" wire:click="retry({{ Js::from($job->id) }})">Retry</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center">There aren't any failed jobs.</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>
</div>
