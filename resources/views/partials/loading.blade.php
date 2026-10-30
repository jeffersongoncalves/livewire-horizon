{{-- Indeterminate bar across the top of the page while the component waits for the server. --}}
<div wire:loading.delay.block @isset($target) wire:target="{{ $target }}" @endisset
    class="fixed inset-x-0 top-0 z-50 h-1 overflow-hidden bg-accent/20">
    <div class="h-full w-1/3 animate-horizon-progress bg-accent"></div>
</div>
