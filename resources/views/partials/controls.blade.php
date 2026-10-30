<div class="inline-flex gap-1">
    @if ($status === 'paused')
        <flux:button size="xs" variant="subtle" icon="play" wire:click="control('continue', '{{ $target }}', {{ Js::from($name) }})">Continue</flux:button>
    @else
        <flux:button size="xs" variant="subtle" icon="pause" wire:click="control('pause', '{{ $target }}', {{ Js::from($name) }})"
            wire:confirm="Pause {{ $name }}? Workers stop picking up new jobs.">Pause</flux:button>
    @endif
    <flux:button size="xs" variant="subtle" icon="arrow-path" wire:click="control('restart', '{{ $target }}', {{ Js::from($name) }})"
        wire:confirm="Restart the workers of {{ $name }}? Running jobs finish first.">Restart</flux:button>
</div>
