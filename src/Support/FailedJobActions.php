<?php

namespace JeffersonGoncalves\LivewireHorizon\Support;

use Laravel\Horizon\Contracts\JobRepository;
use Laravel\Horizon\Jobs\RetryFailedJob;

class FailedJobActions
{
    /**
     * Same as Horizon's retry button, for many jobs at once.
     *
     * @param  array<int, string>  $ids
     */
    public static function retry(array $ids): int
    {
        foreach ($ids as $id) {
            dispatch(new RetryFailedJob($id));
        }

        return count($ids);
    }

    /**
     * Same as `horizon:forget {id}`, for many jobs at once.
     *
     * @param  array<int, string>  $ids
     */
    public static function forget(array $ids): int
    {
        $jobs = app(JobRepository::class);

        foreach ($ids as $id) {
            $jobs->deleteFailed($id);
            app('queue.failer')->forget($id);
        }

        return count($ids);
    }
}
