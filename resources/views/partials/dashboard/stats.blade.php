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

    $tiles = [
        'Jobs Per Minute' => number_format($stats['jobsPerMinute']),
        'Jobs Past '.$period($stats['periods']['recentJobs']) => number_format($stats['recentJobs']),
        'Failed Jobs Past '.$period($stats['periods']['failedJobs']) => number_format($stats['failedJobs']),
        'Total Processes' => number_format($stats['processes']),
        'Max Wait Time' => $wait->isNotEmpty() ? \Carbon\CarbonInterval::seconds((int) $wait->first())->cascade()->forHumans(short: true) : '-',
        'Max Runtime' => $stats['queueWithMaxRuntime'] ?: '-',
        'Max Throughput' => $stats['queueWithMaxThroughput'] ?: '-',
    ];
@endphp

<div class="grid grid-cols-2 gap-px overflow-hidden rounded-xl border border-zinc-900/10 bg-zinc-200 shadow-xs md:grid-cols-4 dark:border-white/10 dark:bg-zinc-700">
    @foreach ($tiles as $label => $value)
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
</div>
