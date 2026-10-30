@php
    use JeffersonGoncalves\LivewireHorizon\LivewireHorizon;

    $rows = array_filter([
        'ID' => $job->id,
        'Queue' => $job->queue,
        'Connection' => $job->connection ?? null,
        'Attempts' => $job->payload->attempts ?? null,
        'Retry of' => $job->payload->retry_of ?? null,
        'Batch' => $job->payload->batchId ?? null,
        'Pushed' => LivewireHorizon::date($job->payload->pushedAt ?? null),
        'Completed' => ($job->status ?? null) === 'completed' ? LivewireHorizon::date($job->completed_at ?? null) : null,
        'Failed' => LivewireHorizon::date($job->failed_at ?? null),
        'Runtime' => LivewireHorizon::runtime($job),
    ], fn ($value) => $value !== null && $value !== '');
@endphp

<flux:card class="space-y-4">
    <dl class="grid grid-cols-1 gap-x-6 gap-y-3 sm:grid-cols-[10rem_1fr]">
        @foreach ($rows as $label => $value)
            <dt><flux:text>{{ $label }}</flux:text></dt>
            <dd>
                @if ($label === 'Batch')
                    <flux:link :href="route('livewire-horizon.batches.show', $value)" wire:navigate>{{ $value }}</flux:link>
                @elseif ($label === 'Retry of')
                    <flux:link :href="route('livewire-horizon.failed.show', $value)" wire:navigate>{{ $value }}</flux:link>
                @else
                    <flux:text variant="strong" class="break-all">{{ $value }}</flux:text>
                @endif
            </dd>
        @endforeach

        @if (collect((array) ($job->payload->tags ?? []))->isNotEmpty())
            <dt><flux:text>Tags</flux:text></dt>
            <dd class="flex flex-wrap gap-1">
                @foreach ($job->payload->tags as $tag)
                    <flux:badge size="sm" :href="route('livewire-horizon.monitoring.tag', $tag)">{{ $tag }}</flux:badge>
                @endforeach
            </dd>
        @endif
    </dl>
</flux:card>

<flux:card class="space-y-3">
    <flux:heading size="lg">Data</flux:heading>
    <pre class="overflow-x-auto rounded-lg bg-[#292d3e] p-4 text-xs text-zinc-100">{{ json_encode($job->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
</flux:card>
