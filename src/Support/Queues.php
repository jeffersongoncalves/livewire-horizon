<?php

namespace JeffersonGoncalves\LivewireHorizon\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laravel\Horizon\Contracts\JobRepository;
use Laravel\Horizon\Contracts\WorkloadRepository;

class Queues
{
    /**
     * Same as `horizon:clear --queue=`: drop the queued jobs and their pending records.
     */
    public static function clear(string $queue): int
    {
        // A comma-joined name is a combined workload row, not a queue that can be cleared.
        abort_if($queue === '' || str_contains($queue, ','), 422);

        $jobs = app(JobRepository::class);

        if (method_exists($jobs, 'purge')) {
            $jobs->purge($queue);
        }

        return (int) app('queue')->connection(self::connection($queue))->clear($queue);
    }

    /**
     * @return array<int, string>
     */
    public static function names(): array
    {
        return collect(app(WorkloadRepository::class)->get())->pluck('name')->sort()->values()->all();
    }

    /**
     * The connection of the supervisor that works the queue in this environment,
     * merged with the defaults the same way Horizon's provisioning plan does.
     */
    public static function connection(string $queue): string
    {
        $defaults = (array) config('horizon.defaults');
        $environment = collect((array) config('horizon.environments'))
            ->first(fn ($supervisors, string $name) => Str::is($name, app()->environment())) ?? [];

        foreach (array_replace_recursive($defaults, (array) $environment) as $supervisor) {
            $queues = explode(',', implode(',', (array) ($supervisor['queue'] ?? [])));

            if (in_array($queue, $queues, true)) {
                return $supervisor['connection'] ?? 'redis';
            }
        }

        return Arr::first($defaults)['connection'] ?? 'redis';
    }
}
