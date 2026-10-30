<?php

namespace JeffersonGoncalves\LivewireHorizon;

use Illuminate\Support\HtmlString;
use Laravel\Horizon\Horizon;
use RuntimeException;

class LivewireHorizon
{
    /**
     * The prebuilt Tailwind + Flux stylesheet, inlined like Horizon's own (no publishing, no build step in the app).
     */
    public static function css(): HtmlString
    {
        if (($css = @file_get_contents(__DIR__.'/../dist/livewire-horizon.css')) === false) {
            throw new RuntimeException('Unable to load the Livewire Horizon CSS.');
        }

        return new HtmlString('<style'.Horizon::$nonceAttribute.'>'.$css.'</style>');
    }

    /**
     * Runtime in seconds between two Horizon timestamps.
     */
    public static function runtime(object $job): ?string
    {
        if (empty($job->completed_at) || empty($job->reserved_at)) {
            return null;
        }

        return number_format((float) $job->completed_at - (float) $job->reserved_at, 2).'s';
    }

    public static function date(int|float|string|null $timestamp): ?string
    {
        return $timestamp ? date('Y-m-d H:i:s', (int) $timestamp) : null;
    }
}
