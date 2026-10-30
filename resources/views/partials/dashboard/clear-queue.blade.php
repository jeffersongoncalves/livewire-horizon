{{-- A comma-joined name is a combined workload row, not a queue that can be cleared. --}}
@if (! str_contains($name, ','))
    <flux:button size="xs" variant="subtle" icon="trash"
        x-on:click="$dispatch('horizon-confirm', { message: {{ Js::from('Delete every job waiting in the ['.$name.'] queue?') }}, action: () => $wire.clearQueue({{ Js::from($name) }}) })">Clear</flux:button>
@endif
