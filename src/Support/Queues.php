<?php

namespace JeffersonGoncalves\LivewireHorizon\Support;

use Illuminate\Support\Arr;
use Laravel\Horizon\Contracts\JobRepository;
use Laravel\Horizon\Contracts\WorkloadRepository;

class Queues
{
    /**
     * Same as `horizon:clear --queue=`: drop the queued jobs and their pending records.
     */
    public static function clear(string $queue): int
    {
        $jobs = app(JobRepository::class);

        if (method_exists($jobs, 'purge')) {
            $jobs->purge($queue);
        }

        return (int) app('queue')->connection(self::connection())->clear($queue);
    }

    /**
     * @return array<int, string>
     */
    public static function names(): array
    {
        return collect(app(WorkloadRepository::class)->get())->pluck('name')->sort()->values()->all();
    }

    protected static function connection(): string
    {
        return Arr::first((array) config('horizon.defaults'))['connection'] ?? 'redis';
    }
}
