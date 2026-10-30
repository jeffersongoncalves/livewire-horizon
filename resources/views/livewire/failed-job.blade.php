<div class="space-y-6">
    <div class="flex flex-wrap items-center gap-2">
        <flux:heading size="xl" class="break-all">{{ $job->name }}</flux:heading>
        <flux:spacer />
        <flux:button size="sm" icon="arrow-path" wire:click="retry">Retry</flux:button>
        <flux:button size="sm" variant="danger" icon="trash" x-on:click="$dispatch('horizon-confirm', { message: 'Delete this failed job?', action: () => $wire.forget() })">Delete</flux:button>
    </div>

    @include('livewire-horizon::partials.job-details')

    <flux:card class="space-y-3">
        <flux:heading size="lg">Exception</flux:heading>
        <pre class="max-h-[32rem] overflow-auto rounded-lg bg-[#292d3e] p-4 text-xs text-zinc-100">{{ $job->exception }}</pre>
    </flux:card>

    @if (! empty($job->context))
        <flux:card class="space-y-3">
            <flux:heading size="lg">Exception Context</flux:heading>
            <pre class="overflow-x-auto rounded-lg bg-[#292d3e] p-4 text-xs text-zinc-100">{{ json_encode($job->context, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
        </flux:card>
    @endif

    @if (collect($job->retried_by)->isNotEmpty())
        <flux:card class="space-y-3">
            <flux:heading size="lg">Recent Retries</flux:heading>
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Job</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column align="end">Retry Time</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach ($job->retried_by as $retry)
                        <flux:table.row :key="$retry->id">
                            <flux:table.cell>
                                <flux:link :href="route($retry->status === 'failed' ? 'livewire-horizon.failed.show' : 'livewire-horizon.jobs.show', $retry->id)" wire:navigate>{{ $retry->id }}</flux:link>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge size="sm" :color="match ($retry->status) { 'completed' => 'green', 'failed' => 'red', default => 'zinc' }">{{ ucfirst($retry->status) }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell align="end">{{ \JeffersonGoncalves\LivewireHorizon\LivewireHorizon::date($retry->retried_at) }}</flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </flux:card>
    @endif
</div>
