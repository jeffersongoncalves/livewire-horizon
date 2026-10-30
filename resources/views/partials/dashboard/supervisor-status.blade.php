@if ($supervisor->status !== 'running')
    <flux:badge size="sm" :color="$supervisor->status === 'paused' ? 'amber' : 'zinc'" class="ms-2">{{ ucfirst($supervisor->status) }}</flux:badge>
@endif
