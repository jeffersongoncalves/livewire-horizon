<div class="space-y-6">
    <flux:heading size="xl" class="break-all">{{ $id }}</flux:heading>

    @foreach (['throughput' => 'Throughput (jobs per minute)', 'runtime' => 'Average runtime (seconds)'] as $field => $label)
        <flux:card class="space-y-4">
            <flux:heading size="lg">{{ $label }}</flux:heading>

            @if (count($snapshots) < 2)
                <flux:text>Not enough snapshots yet. Schedule <code>horizon:snapshot</code> to record them.</flux:text>
            @else
                <flux:chart :value="$snapshots" class="aspect-[4/1]">
                    <flux:chart.svg>
                        <flux:chart.line :field="$field" class="text-accent" />
                        <flux:chart.area :field="$field" class="text-accent/10" />
                        <flux:chart.axis axis="x" field="time">
                            <flux:chart.axis.tick />
                            <flux:chart.axis.line />
                        </flux:chart.axis>
                        <flux:chart.axis axis="y">
                            <flux:chart.axis.grid />
                            <flux:chart.axis.tick />
                        </flux:chart.axis>
                        <flux:chart.cursor />
                    </flux:chart.svg>
                    <flux:chart.tooltip>
                        <flux:chart.tooltip.heading field="time" />
                        <flux:chart.tooltip.value :field="$field" :label="ucfirst($field)" />
                    </flux:chart.tooltip>
                </flux:chart>
            @endif
        </flux:card>
    @endforeach
</div>
