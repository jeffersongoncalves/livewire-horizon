<div class="space-y-6">
    <flux:heading size="xl" class="break-all">{{ $tag }}</flux:heading>

    <flux:card class="space-y-4">
        <flux:tabs wire:model.live="tab">
            <flux:tab name="jobs">Recent Jobs</flux:tab>
            <flux:tab name="failed">Failed Jobs</flux:tab>
        </flux:tabs>

        <flux:table :paginate="$jobs">
            <flux:table.columns>
                <flux:table.column>Job</flux:table.column>
                <flux:table.column align="end">Queued</flux:table.column>
                <flux:table.column align="end">{{ $tab === 'failed' ? 'Failed' : 'Completed' }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($jobs as $job)
                    <flux:table.row :key="$job->id">
                        <flux:table.cell>@include('livewire-horizon::partials.job-name', ['failed' => $tab === 'failed'])</flux:table.cell>
                        <flux:table.cell align="end">{{ \JeffersonGoncalves\LivewireHorizon\LivewireHorizon::date($job->payload->pushedAt ?? null) }}</flux:table.cell>
                        <flux:table.cell align="end">{{ \JeffersonGoncalves\LivewireHorizon\LivewireHorizon::date($tab === 'failed' ? ($job->failed_at ?? null) : ($job->completed_at ?? null)) ?? '-' }}</flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="3" class="text-center">There aren't any jobs.</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>
</div>
