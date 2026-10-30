<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Dashboard path and domain
    |--------------------------------------------------------------------------
    |
    | Where the dashboard lives. Access uses the same gate as Horizon
    | (Horizon::auth / the viewHorizon gate).
    |
    */

    'path' => env('LIVEWIRE_HORIZON_PATH', 'livewire-horizon'),

    'domain' => env('LIVEWIRE_HORIZON_DOMAIN'),

    'middleware' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Search limit
    |--------------------------------------------------------------------------
    |
    | Horizon keeps jobs in Redis without an index, so searching scans the
    | stored jobs. This caps how many jobs a single search (or "retry all" /
    | "delete all" on a filtered list) will look at.
    |
    */

    'search_limit' => 5000,

    /*
    |--------------------------------------------------------------------------
    | Polling
    |--------------------------------------------------------------------------
    |
    | How often the dashboard refreshes itself, in seconds.
    |
    */

    'poll' => 5,

];
