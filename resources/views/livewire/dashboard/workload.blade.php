{{-- Blaze folds Flux components at compile time: keep @if/@foreach out of their slots (use partials). --}}
<div wire:poll.{{ config('livewire-horizon.poll', 5) }}s>
    <flux:card class="space-y-4">
        <flux:heading size="lg">Current Workload</flux:heading>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>Queue</flux:table.column>
                <flux:table.column align="end">Jobs</flux:table.column>
                <flux:table.column align="end">Processes</flux:table.column>
                <flux:table.column align="end">Wait</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @include('livewire-horizon::partials.dashboard.workload-rows')
            </flux:table.rows>
        </flux:table>
    </flux:card>
</div>
