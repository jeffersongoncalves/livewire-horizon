<div class="space-y-6">
    @include('livewire-horizon::partials.loading')
    <flux:heading size="xl">Metrics</flux:heading>

    <flux:card class="space-y-4">
        <flux:tabs wire:model.live="type">
            <flux:tab name="jobs">Jobs</flux:tab>
            <flux:tab name="queues">Queues</flux:tab>
        </flux:tabs>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ $type === 'jobs' ? 'Job' : 'Queue' }}</flux:table.column>
                <flux:table.column align="end">Throughput</flux:table.column>
                <flux:table.column align="end">Avg. Runtime</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($items as $item)
                    <flux:table.row :key="$item->name">
                        <flux:table.cell class="whitespace-normal">
                            <flux:link :href="route('livewire-horizon.metrics.show', [$type, $item->name])" wire:navigate class="break-all">{{ $item->name }}</flux:link>
                        </flux:table.cell>
                        <flux:table.cell align="end">{{ number_format($item->throughput) }}</flux:table.cell>
                        <flux:table.cell align="end">{{ number_format($item->runtime / 1000, 3) }}s</flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="3" class="text-center">There aren't any metrics yet. Schedule <code>horizon:snapshot</code> to record them.</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>
</div>
