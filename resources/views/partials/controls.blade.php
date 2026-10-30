{{-- Inactive supervisors are only in the provisioning plan: there is no process to control. --}}
@if ($status !== 'inactive')
    <div class="inline-flex gap-1">
        @if ($status === 'paused')
            <flux:button size="xs" variant="subtle" icon="play" wire:click="control('continue', '{{ $target }}', {{ Js::from($name) }})">Continue</flux:button>
        @else
            <flux:button size="xs" variant="subtle" icon="pause" x-on:click="$dispatch('horizon-confirm', { message: {{ Js::from('Pause '.$name.'? Workers stop picking up new jobs.') }}, action: () => $wire.control('pause', '{{ $target }}', {{ Js::from($name) }}) })">Pause</flux:button>
        @endif
        <flux:button size="xs" variant="subtle" icon="arrow-path" x-on:click="$dispatch('horizon-confirm', { message: {{ Js::from('Restart the workers of '.$name.'? Running jobs finish first.') }}, action: () => $wire.control('restart', '{{ $target }}', {{ Js::from($name) }}) })">Restart</flux:button>
    </div>
@endif
