<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $livewireHorizonTitle ?? 'Dashboard' }} · Horizon{{ config('horizon.name') ? ' - '.config('horizon.name') : '' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600" rel="stylesheet" />

    {{ \JeffersonGoncalves\LivewireHorizon\LivewireHorizon::css() }}
    @livewireStyles
    @fluxAppearance
</head>
<body class="min-h-screen bg-zinc-100 font-sans antialiased dark:bg-zinc-900">
    <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.header>
            <a href="{{ route('livewire-horizon.dashboard') }}" wire:navigate class="flex items-center gap-2 text-zinc-700 dark:text-white">
                <flux:icon.queue-list class="text-accent" />
                <span class="text-lg"><strong>Laravel</strong> Horizon</span>
            </a>
            <flux:sidebar.collapse class="lg:hidden" />
        </flux:sidebar.header>

        <flux:sidebar.nav>
            <flux:sidebar.item icon="squares-2x2" :href="route('livewire-horizon.dashboard')" :current="request()->routeIs('livewire-horizon.dashboard')" wire:navigate>Dashboard</flux:sidebar.item>
            <flux:sidebar.item icon="magnifying-glass" :href="route('livewire-horizon.monitoring')" :current="request()->routeIs('livewire-horizon.monitoring*')" wire:navigate>Monitoring</flux:sidebar.item>
            <flux:sidebar.item icon="chart-bar" :href="route('livewire-horizon.metrics')" :current="request()->routeIs('livewire-horizon.metrics*')" wire:navigate>Metrics</flux:sidebar.item>
            <flux:sidebar.item icon="bars-3" :href="route('livewire-horizon.batches')" :current="request()->routeIs('livewire-horizon.batches*')" wire:navigate>Batches</flux:sidebar.item>
            <flux:sidebar.item icon="pause-circle" :href="route('livewire-horizon.jobs', 'pending')" :current="request()->route('type') === 'pending'" wire:navigate>Pending Jobs</flux:sidebar.item>
            <flux:sidebar.item icon="check-circle" :href="route('livewire-horizon.jobs', 'completed')" :current="request()->route('type') === 'completed'" wire:navigate>Completed Jobs</flux:sidebar.item>
            <flux:sidebar.item icon="bell-slash" :href="route('livewire-horizon.jobs', 'silenced')" :current="request()->route('type') === 'silenced'" wire:navigate>Silenced Jobs</flux:sidebar.item>
            <flux:sidebar.item icon="exclamation-circle" :href="route('livewire-horizon.failed')" :current="request()->routeIs('livewire-horizon.failed*')" wire:navigate>Failed Jobs</flux:sidebar.item>
        </flux:sidebar.nav>

        <flux:sidebar.spacer />

        <flux:radio.group x-data variant="segmented" x-model="$flux.appearance" size="sm">
            <flux:radio value="light" icon="sun" />
            <flux:radio value="dark" icon="moon" />
            <flux:radio value="system" icon="computer-desktop" />
        </flux:radio.group>
    </flux:sidebar>

    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
    </flux:header>

    <flux:main container>
        @if (app()->isDownForMaintenance())
            <flux:callout variant="warning" icon="exclamation-triangle" class="mb-6"
                heading='This application is in "maintenance mode". Queued jobs may not be processed unless your worker is using the "force" flag.' />
        @endif

        {{ $slot }}
    </flux:main>

    {{-- Shared confirmation for destructive actions: buttons dispatch horizon-confirm with the message and the $wire call to run.
         The bar follows the confirmed call itself, so components that poll don't flash it. --}}
    <div x-data="{ message: '', action: null, busy: false }"
        x-on:horizon-confirm.window="message = $event.detail.message; action = $event.detail.action; $flux.modal('horizon-confirm').show()">
        @include('livewire-horizon::partials.loading', ['when' => 'busy'])

        <flux:modal name="horizon-confirm" class="md:w-96">
            <div class="space-y-6">
                <flux:heading size="lg">Are you sure?</flux:heading>
                <flux:text x-text="message"></flux:text>
                <div class="flex gap-2">
                    <flux:spacer />
                    <flux:modal.close>
                        <flux:button variant="ghost">Cancel</flux:button>
                    </flux:modal.close>
                    <flux:button variant="danger" x-on:click="$flux.modal('horizon-confirm').close(); busy = true; Promise.resolve(action()).finally(() => busy = false)">Confirm</flux:button>
                </div>
            </div>
        </flux:modal>
    </div>

    @persist('toast')
        <flux:toast />
    @endpersist

    @livewireScripts
    @fluxScripts
</body>
</html>
