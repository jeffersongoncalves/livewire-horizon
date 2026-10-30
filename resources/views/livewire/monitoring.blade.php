<div class="space-y-6">
    <div class="flex items-center gap-3">
        <flux:heading size="xl">Monitoring</flux:heading>
        <flux:spacer />
        <flux:modal.trigger name="monitor-tag">
            <flux:button size="sm" variant="primary" icon="plus">Monitor tag</flux:button>
        </flux:modal.trigger>
    </div>

    <flux:card>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Tag</flux:table.column>
                <flux:table.column align="end">Jobs</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($tags as $item)
                    <flux:table.row :key="$item['tag']">
                        <flux:table.cell>
                            <flux:link :href="route('livewire-horizon.monitoring.tag', $item['tag'])" wire:navigate>{{ $item['tag'] }}</flux:link>
                        </flux:table.cell>
                        <flux:table.cell align="end">{{ number_format($item['count']) }}</flux:table.cell>
                        <flux:table.cell align="end">
                            <flux:button size="xs" variant="subtle" icon="x-mark" wire:click="stopMonitoring({{ Js::from($item['tag']) }})"
                                wire:confirm="Stop monitoring [{{ $item['tag'] }}]? Its stored jobs are deleted.">Stop</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="3" class="text-center">You're not monitoring any tags.</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>

    <flux:modal name="monitor-tag" class="md:w-96">
        <form wire:submit="monitor" class="space-y-6">
            <flux:heading size="lg">Monitor tag</flux:heading>
            <flux:input wire:model="tag" label="Tag" placeholder="App\Models\User:6352" autofocus />
            <div class="flex">
                <flux:spacer />
                <flux:button type="submit" variant="primary">Monitor</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
