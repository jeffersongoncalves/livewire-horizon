{{-- Blaze folds Flux components at compile time: keep @if/@foreach out of their slots (use partials). --}}
<div class="space-y-6">
    @include('livewire-horizon::partials.auto-load')
    @foreach ($masters as $master)
        {{-- wire:key on a plain element: Livewire 4 rewrites it inside loops and breaks a component tag. --}}
        <div wire:key="master-{{ $master->name }}">
        <flux:card class="space-y-4">
            <div class="flex flex-wrap items-center gap-3">
                <flux:heading size="lg">{{ $master->name }}</flux:heading>
                <flux:badge size="sm" :color="$master->status === 'running' ? 'green' : 'amber'">{{ ucfirst($master->status) }}</flux:badge>
                <flux:spacer />
                @include('livewire-horizon::partials.controls', ['target' => 'master', 'name' => $master->name, 'status' => $master->status])
            </div>

            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Supervisor</flux:table.column>
                    <flux:table.column>Queues</flux:table.column>
                    <flux:table.column align="end">Processes</flux:table.column>
                    <flux:table.column align="end">Balancing</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @include('livewire-horizon::partials.dashboard.supervisor-rows')
                </flux:table.rows>
            </flux:table>
        </flux:card>
        </div>
    @endforeach
</div>
