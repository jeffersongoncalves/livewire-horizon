<div class="flex flex-col gap-3 sm:flex-row">
    <flux:input wire:model.live.debounce.400ms="search" icon="magnifying-glass" clearable
        placeholder="Search by job, ID, queue, tag or payload..." class="sm:flex-1" />

    <flux:select wire:model.live="queue" class="sm:max-w-56">
        <flux:select.option value="">All queues</flux:select.option>
        @foreach ($queues as $name)
            <flux:select.option :value="$name">{{ $name }}</flux:select.option>
        @endforeach
    </flux:select>
</div>

@if ($search !== '' || $queue !== '')
    <flux:text size="sm">
        {{ number_format($jobs->total()) }} matching jobs. Search scans up to {{ number_format(config('livewire-horizon.search_limit')) }} stored jobs.
    </flux:text>
@endif
