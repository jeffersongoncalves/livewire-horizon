<div class="space-y-6">
    @include('livewire-horizon::partials.auto-load')
    <div class="flex flex-wrap items-center gap-3">
        <flux:heading size="xl" class="break-all">{{ $job->name }}</flux:heading>
        <flux:badge size="sm" :color="match ($job->status) { 'completed' => 'green', 'failed' => 'red', 'reserved' => 'blue', default => 'zinc' }">{{ ucfirst($job->status) }}</flux:badge>
    </div>

    @include('livewire-horizon::partials.job-details')
</div>
