@forelse ($workload as $queue)
    <flux:table.row :key="$queue['name']">
        <flux:table.cell variant="strong">{{ str_replace(',', ', ', $queue['name']) }}</flux:table.cell>
        <flux:table.cell align="end">{{ number_format($queue['length']) }}</flux:table.cell>
        <flux:table.cell align="end">{{ number_format($queue['processes']) }}</flux:table.cell>
        <flux:table.cell align="end">{{ \Carbon\CarbonInterval::seconds((int) $queue['wait'])->cascade()->forHumans(short: true) }}</flux:table.cell>
        <flux:table.cell align="end">@include('livewire-horizon::partials.dashboard.clear-queue', ['name' => $queue['name']])</flux:table.cell>
    </flux:table.row>
    @foreach ($queue['split_queues'] ?? [] as $split)
        <flux:table.row :key="$queue['name'].$split['name']">
            <flux:table.cell class="ps-8">{{ $split['name'] }}</flux:table.cell>
            <flux:table.cell align="end">{{ number_format($split['length']) }}</flux:table.cell>
            <flux:table.cell align="end">-</flux:table.cell>
            <flux:table.cell align="end">{{ \Carbon\CarbonInterval::seconds((int) $split['wait'])->cascade()->forHumans(short: true) }}</flux:table.cell>
            <flux:table.cell align="end">@include('livewire-horizon::partials.dashboard.clear-queue', ['name' => $split['name']])</flux:table.cell>
        </flux:table.row>
    @endforeach
@empty
    <flux:table.row>
        <flux:table.cell colspan="5" class="text-center">No queues are being processed.</flux:table.cell>
    </flux:table.row>
@endforelse
