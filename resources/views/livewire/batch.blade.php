<div class="space-y-6">
    <div class="flex flex-wrap items-center gap-2">
        <flux:heading size="xl" class="break-all">{{ $batch->name ?: $batch->id }}</flux:heading>
        @include('livewire-horizon::partials.batch-status')
        <flux:spacer />
        @if ($batch->failedJobs > 0)
            <flux:button size="sm" icon="arrow-path" wire:click="retry">Retry failed jobs</flux:button>
        @endif
    </div>

    <flux:card>
        <dl class="grid grid-cols-1 gap-x-6 gap-y-3 sm:grid-cols-[10rem_1fr]">
            @foreach ([
                'ID' => $batch->id,
                'Queue' => $batch->options['queue'] ?? 'default',
                'Connection' => $batch->options['connection'] ?? null,
                'Created' => $batch->createdAt->format('Y-m-d H:i:s'),
                'Finished' => $batch->finishedAt?->format('Y-m-d H:i:s'),
                'Cancelled' => $batch->cancelledAt?->format('Y-m-d H:i:s'),
                'Total Jobs' => number_format($batch->totalJobs),
                'Pending Jobs' => number_format($batch->pendingJobs),
                'Failed Jobs' => number_format($batch->failedJobs),
                'Completion' => $batch->progress().'%',
            ] as $label => $value)
                @if ($value !== null)
                    <dt><flux:text>{{ $label }}</flux:text></dt>
                    <dd><flux:text variant="strong" class="break-all">{{ $value }}</flux:text></dd>
                @endif
            @endforeach
        </dl>
    </flux:card>

    @if ($failedJobs->isNotEmpty())
        <flux:card class="space-y-3">
            <flux:heading size="lg">Failed Jobs</flux:heading>
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Job</flux:table.column>
                    <flux:table.column align="end">Failed</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach ($failedJobs as $job)
                        <flux:table.row :key="$job->id">
                            <flux:table.cell>@include('livewire-horizon::partials.job-name', ['failed' => true])</flux:table.cell>
                            <flux:table.cell align="end">{{ \JeffersonGoncalves\LivewireHorizon\LivewireHorizon::date($job->failed_at ?? null) }}</flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </flux:card>
    @endif
</div>
