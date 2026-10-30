@php
    $statusColor = ['running' => 'green', 'paused' => 'amber', 'inactive' => 'red'][$stats['status']] ?? 'zinc';
    $wait = collect($stats['wait']);

    // Horizon's trim periods are in minutes: 60 => "Hour", 10080 => "7 Days".
    $period = function (int $minutes): string {
        [$amount, $unit] = match (true) {
            $minutes % 1440 === 0 => [$minutes / 1440, 'Day'],
            $minutes % 60 === 0 => [$minutes / 60, 'Hour'],
            default => [$minutes, 'Minute'],
        };

        return $amount === 1 ? $unit : $amount.' '.\Illuminate\Support\Str::plural($unit);
    };
@endphp

<div wire:poll.{{ config('livewire-horizon.poll', 5) }}s class="space-y-6">
    <flux:heading size="xl">Overview</flux:heading>

    <flux:card class="grid grid-cols-2 gap-px overflow-hidden p-0! md:grid-cols-4 bg-zinc-200 dark:bg-zinc-700">
        @foreach ([
            ['Jobs Per Minute', number_format($stats['jobsPerMinute'])],
            ['Jobs Past '.$period($stats['periods']['recentJobs']), number_format($stats['recentJobs'])],
            ['Failed Jobs Past '.$period($stats['periods']['failedJobs']), number_format($stats['failedJobs'])],
            ['Total Processes', number_format($stats['processes'])],
            ['Max Wait Time', $wait->isNotEmpty() ? \Carbon\CarbonInterval::seconds((int) $wait->first())->cascade()->forHumans(short: true) : '-'],
            ['Max Runtime', $stats['queueWithMaxRuntime'] ?: '-'],
            ['Max Throughput', $stats['queueWithMaxThroughput'] ?: '-'],
        ] as [$label, $value])
            <div class="bg-white p-4 dark:bg-zinc-800">
                <flux:text size="sm">{{ $label }}</flux:text>
                <flux:heading size="lg" class="mt-1 truncate">{{ $value }}</flux:heading>
            </div>
        @endforeach

        <div class="bg-white p-4 dark:bg-zinc-800">
            <flux:text size="sm">Status</flux:text>
            <flux:badge :color="$statusColor" size="sm" class="mt-1">{{ ucfirst($stats['status']) }}</flux:badge>
            @if ($stats['status'] === 'running' && $stats['pausedMasters'] > 0)
                <flux:text size="sm" class="mt-1">{{ $stats['pausedMasters'] }} paused</flux:text>
            @endif
        </div>
    </flux:card>

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
                @forelse ($workload as $queue)
                    <flux:table.row :key="$queue['name']">
                        <flux:table.cell variant="strong">{{ str_replace(',', ', ', $queue['name']) }}</flux:table.cell>
                        <flux:table.cell align="end">{{ number_format($queue['length']) }}</flux:table.cell>
                        <flux:table.cell align="end">{{ number_format($queue['processes']) }}</flux:table.cell>
                        <flux:table.cell align="end">{{ \Carbon\CarbonInterval::seconds((int) $queue['wait'])->cascade()->forHumans(short: true) }}</flux:table.cell>
                        <flux:table.cell align="end">
                            @unless (str_contains($queue['name'], ','))
                                <flux:button size="xs" variant="subtle" icon="trash"
                                    wire:click="clearQueue({{ Js::from($queue['name']) }})"
                                    wire:confirm="Delete every job waiting in the [{{ $queue['name'] }}] queue?">Clear</flux:button>
                            @endunless
                        </flux:table.cell>
                    </flux:table.row>
                    @foreach ($queue['split_queues'] ?? [] as $split)
                        <flux:table.row :key="$queue['name'].$split['name']">
                            <flux:table.cell class="ps-8">{{ $split['name'] }}</flux:table.cell>
                            <flux:table.cell align="end">{{ number_format($split['length']) }}</flux:table.cell>
                            <flux:table.cell align="end">-</flux:table.cell>
                            <flux:table.cell align="end">{{ \Carbon\CarbonInterval::seconds((int) $split['wait'])->cascade()->forHumans(short: true) }}</flux:table.cell>
                            <flux:table.cell align="end">
                                <flux:button size="xs" variant="subtle" icon="trash"
                                    wire:click="clearQueue({{ Js::from($split['name']) }})"
                                    wire:confirm="Delete every job waiting in the [{{ $split['name'] }}] queue?">Clear</flux:button>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center">No queues are being processed.</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>

    @foreach ($masters as $master)
        <flux:card class="space-y-4" wire:key="master-{{ $master->name }}">
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
                    @foreach ($master->supervisors as $supervisor)
                        <flux:table.row :key="$supervisor->name">
                            <flux:table.cell variant="strong">
                                {{ \Illuminate\Support\Str::after($supervisor->name, ':') }}
                                @if ($supervisor->status !== 'running')
                                    <flux:badge size="sm" :color="$supervisor->status === 'paused' ? 'amber' : 'zinc'" class="ms-2">{{ ucfirst($supervisor->status) }}</flux:badge>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>{{ str_replace(',', ', ', $supervisor->options['queue'] ?? '') }}</flux:table.cell>
                            <flux:table.cell align="end">{{ number_format(collect($supervisor->processes)->sum()) }}</flux:table.cell>
                            <flux:table.cell align="end">{{ ucfirst($supervisor->options['balance'] ?? 'disabled') ?: 'Disabled' }}</flux:table.cell>
                            <flux:table.cell align="end">
                                @if ($supervisor->status !== 'inactive')
                                    @include('livewire-horizon::partials.controls', ['target' => 'supervisor', 'name' => $supervisor->name, 'status' => $supervisor->status])
                                @endif
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </flux:card>
    @endforeach
</div>
