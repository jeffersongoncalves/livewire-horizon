@foreach ($master->supervisors as $supervisor)
    <flux:table.row :key="$supervisor->name">
        <flux:table.cell variant="strong">
            {{ \Illuminate\Support\Str::after($supervisor->name, ':') }}
            @include('livewire-horizon::partials.dashboard.supervisor-status')
        </flux:table.cell>
        <flux:table.cell>{{ str_replace(',', ', ', $supervisor->options['queue'] ?? '') }}</flux:table.cell>
        <flux:table.cell align="end">{{ number_format(collect($supervisor->processes)->sum()) }}</flux:table.cell>
        <flux:table.cell align="end">{{ ucfirst($supervisor->options['balance'] ?? '') ?: 'Disabled' }}</flux:table.cell>
        <flux:table.cell align="end">@include('livewire-horizon::partials.controls', ['target' => 'supervisor', 'name' => $supervisor->name, 'status' => $supervisor->status])</flux:table.cell>
    </flux:table.row>
@endforeach
