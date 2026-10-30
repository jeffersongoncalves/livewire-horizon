@php
    $tags = collect($job->payload->tags ?? [])->filter(fn ($tag) => is_string($tag));
@endphp

<div class="flex flex-col gap-1 whitespace-normal">
    <flux:link :href="route($failed ?? false ? 'livewire-horizon.failed.show' : 'livewire-horizon.jobs.show', $job->id)" wire:navigate variant="ghost" class="font-medium break-all">
        {{ $job->name }}
    </flux:link>
    <div class="flex flex-wrap items-center gap-1 text-sm text-zinc-500 dark:text-zinc-400">
        <span>Queue: {{ $job->queue }}</span>
        @if (($job->payload->attempts ?? 0) > 1)
            <span>· Attempts: {{ $job->payload->attempts }}</span>
        @endif
        @if (! empty($job->payload->retry_of))
            <span>· Retry of {{ \Illuminate\Support\Str::limit($job->payload->retry_of, 8, '') }}</span>
        @endif
        @foreach ($tags->take(5) as $tag)
            <flux:badge size="sm" :href="route('livewire-horizon.monitoring.tag', $tag)">{{ \Illuminate\Support\Str::limit($tag, 40) }}</flux:badge>
        @endforeach
        @if ($tags->count() > 5)
            <span>+{{ $tags->count() - 5 }}</span>
        @endif
    </div>
</div>
