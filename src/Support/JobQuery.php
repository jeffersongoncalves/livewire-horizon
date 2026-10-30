<?php

namespace JeffersonGoncalves\LivewireHorizon\Support;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Laravel\Horizon\Contracts\JobRepository;

/**
 * Pages, searches and filters the job lists Horizon keeps in Redis.
 */
class JobQuery
{
    /** Horizon's repository always returns chunks of 50 jobs. */
    public const PER_PAGE = 50;

    public function __construct(protected JobRepository $jobs) {}

    /**
     * @return LengthAwarePaginator<int, object>
     */
    public function paginate(string $type, int $page, string $search = '', string $queue = ''): LengthAwarePaginator
    {
        $page = max(1, $page);

        if ($search === '' && $queue === '') {
            $jobs = $this->chunk($type, ($page - 1) * self::PER_PAGE - 1)->map(static::decode(...));

            return new LengthAwarePaginator($jobs->values(), $this->count($type), self::PER_PAGE, $page);
        }

        $matches = $this->matching($type, $search, $queue);

        return new LengthAwarePaginator(
            $matches->forPage($page, self::PER_PAGE)->map(static::decode(...))->values(),
            $matches->count(),
            self::PER_PAGE,
            $page,
        );
    }

    /**
     * Raw jobs (payload still JSON) that match the search and queue.
     *
     * ponytail: linear scan, Horizon has no index; a filtered scan is capped by
     * livewire-horizon.search_limit, an unfiltered one returns every stored job.
     *
     * @return Collection<int, object>
     */
    public function matching(string $type, string $search = '', string $queue = ''): Collection
    {
        $total = $search === '' && $queue === ''
            ? $this->count($type)
            : min($this->count($type), (int) config('livewire-horizon.search_limit', 5000));
        $matches = collect();

        for ($index = -1; $index + 1 < $total; $index += self::PER_PAGE) {
            $matches = $matches->merge(
                $this->chunk($type, $index)->filter(fn (object $job) => $this->matches($job, $search, $queue))
            );
        }

        return $matches->values();
    }

    public function count(string $type): int
    {
        return (int) match ($type) {
            'pending' => $this->jobs->countPending(),
            'completed' => $this->jobs->countCompleted(),
            'silenced' => $this->jobs->countSilenced(),
            'failed' => $this->jobs->countFailed(),
            default => throw new InvalidArgumentException("Unknown job type [{$type}]."),
        };
    }

    /**
     * @return Collection<int, object>
     */
    protected function chunk(string $type, int $index): Collection
    {
        // The contract types the index as a string (it arrives from the query string).
        $afterIndex = (string) $index;

        return collect(match ($type) {
            'pending' => $this->jobs->getPending($afterIndex),
            'completed' => $this->jobs->getCompleted($afterIndex),
            'silenced' => $this->jobs->getSilenced($afterIndex),
            'failed' => $this->jobs->getFailed($afterIndex),
            default => throw new InvalidArgumentException("Unknown job type [{$type}]."),
        });
    }

    protected function matches(object $job, string $search, string $queue): bool
    {
        if ($queue !== '' && ($job->queue ?? null) !== $queue) {
            return false;
        }

        return $search === '' || Str::contains(
            implode(' ', [$job->id ?? '', $job->name ?? '', $job->queue ?? '', $job->payload ?? '']),
            $search,
            ignoreCase: true,
        );
    }

    public static function decode(object $job): object
    {
        $job = clone $job;
        $job->payload = is_string($job->payload ?? null) ? json_decode($job->payload) : ($job->payload ?? null);

        return $job;
    }
}
