<div class="space-y-6">
    @include('livewire-horizon::partials.auto-load')
    @include('livewire-horizon::partials.loading')
    <div class="flex flex-wrap items-center gap-3">
        <flux:heading size="xl">{{ ucfirst($type) }} Jobs</flux:heading>
        <flux:spacer />
        @if ($type === 'pending' && $queue !== '')
            <flux:button size="sm" variant="danger" icon="trash"
                x-on:click="$dispatch('horizon-confirm', { message: {{ Js::from('Delete every job waiting in the ['.$queue.'] queue?') }}, action: () => $wire.clearQueue() })">Clear queue</flux:button>
        @endif
    </div>

    <flux:card class="space-y-4">
        @include('livewire-horizon::partials.filters')

        <flux:table :paginate="$jobs">
            <flux:table.columns>
                <flux:table.column>Job</flux:table.column>
                <flux:table.column align="end">Queued</flux:table.column>
                @if ($type !== 'pending')
                    <flux:table.column align="end">Completed</flux:table.column>
                    <flux:table.column align="end">Runtime</flux:table.column>
                @endif
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($jobs as $job)
                    <flux:table.row :key="$job->id">
                        <flux:table.cell>@include('livewire-horizon::partials.job-name')</flux:table.cell>
                        <flux:table.cell align="end">{{ \JeffersonGoncalves\LivewireHorizon\LivewireHorizon::date($job->payload->pushedAt ?? null) }}</flux:table.cell>
                        @if ($type !== 'pending')
                            <flux:table.cell align="end">{{ \JeffersonGoncalves\LivewireHorizon\LivewireHorizon::date($job->completed_at ?? null) }}</flux:table.cell>
                            <flux:table.cell align="end">{{ \JeffersonGoncalves\LivewireHorizon\LivewireHorizon::runtime($job) ?? '-' }}</flux:table.cell>
                        @endif
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="4" class="text-center">There aren't any jobs.</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>
</div>
