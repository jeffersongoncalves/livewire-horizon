@if ($batch->cancelledAt)
    <flux:badge size="sm" color="zinc">Cancelled</flux:badge>
@elseif ($batch->failedJobs > 0 && $batch->totalJobs === $batch->processedJobs() + $batch->failedJobs)
    <flux:badge size="sm" color="red">Failures</flux:badge>
@elseif ($batch->finishedAt)
    <flux:badge size="sm" color="green">Finished</flux:badge>
@elseif ($batch->failedJobs > 0)
    <flux:badge size="sm" color="amber">Pending with failures</flux:badge>
@else
    <flux:badge size="sm" color="blue">Pending</flux:badge>
@endif
