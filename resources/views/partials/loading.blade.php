{{-- Indeterminate bar across the top of the page while the server works: on the component's requests, or while the Alpine expression in $when is true. --}}
<div @isset($when) x-show="{{ $when }}" style="display: none" @else wire:loading.delay.block @endisset
    class="fixed inset-x-0 top-0 z-50 h-1 overflow-hidden bg-accent/20">
    <div class="h-full w-1/3 animate-horizon-progress bg-accent"></div>
</div>
