<div class="space-y-6">
    @include('livewire-horizon::partials.loading')
    <flux:heading size="xl">Batches</flux:heading>

    <flux:card class="space-y-4">
        <flux:input wire:model.live.debounce.400ms="search" icon="magnifying-glass" clearable placeholder="Search by name or ID..." />

        <flux:table>
            <flux:table.columns>
                <flux:table.column>Batch</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column align="end">Size</flux:table.column>
                <flux:table.column align="end">Completion</flux:table.column>
                <flux:table.column align="end">Created</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($batches as $batch)
                    <flux:table.row :key="$batch->id">
                        <flux:table.cell>
                            <flux:link :href="route('livewire-horizon.batches.show', $batch->id)" wire:navigate>{{ $batch->name ?: $batch->id }}</flux:link>
                        </flux:table.cell>
                        <flux:table.cell>@include('livewire-horizon::partials.batch-status')</flux:table.cell>
                        <flux:table.cell align="end">{{ number_format($batch->totalJobs) }}</flux:table.cell>
                        <flux:table.cell align="end">{{ $batch->progress() }}%</flux:table.cell>
                        <flux:table.cell align="end">{{ $batch->createdAt->format('Y-m-d H:i:s') }}</flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center">There aren't any batches.</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <div class="flex gap-2">
            @if ($before !== '')
                <flux:button size="sm" variant="subtle" wire:click="$set('before', '')">Newest</flux:button>
            @endif
            <flux:spacer />
            @if ($batches->count() >= 50)
                <flux:button size="sm" variant="subtle" icon-trailing="chevron-right" wire:click="$set('before', {{ Js::from($batches->last()->id) }})">Older</flux:button>
            @endif
        </div>
    </flux:card>
</div>
