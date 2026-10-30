{{-- A comma-joined name is a combined workload row, not a queue that can be cleared. --}}
@if (! str_contains($name, ','))
    <flux:button size="xs" variant="subtle" icon="trash"
        wire:click="clearQueue({{ Js::from($name) }})"
        wire:confirm="Delete every job waiting in the [{{ $name }}] queue?">Clear</flux:button>
@endif
