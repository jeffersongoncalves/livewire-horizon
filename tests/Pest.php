<?php

use JeffersonGoncalves\LivewireHorizon\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

/**
 * A job as Horizon's RedisJobRepository returns it (payload still JSON).
 *
 * @param  array<string, mixed>  $attributes
 */
function horizonJob(string $id, string $name = 'App\\Jobs\\SendInvoice', string $queue = 'default', array $attributes = []): object
{
    return (object) array_merge([
        'id' => $id,
        'connection' => 'redis',
        'queue' => $queue,
        'name' => $name,
        'status' => 'completed',
        'payload' => json_encode(['displayName' => $name, 'tags' => [], 'pushedAt' => 1700000000, 'attempts' => 1, 'data' => ['customer' => "customer-{$id}"]]),
        'exception' => null,
        'context' => null,
        'failed_at' => null,
        'completed_at' => 1700000001,
        'reserved_at' => 1700000000.5,
        'retried_by' => null,
    ], $attributes);
}
