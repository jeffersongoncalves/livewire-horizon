<?php

namespace JeffersonGoncalves\LivewireHorizon\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \JeffersonGoncalves\LivewireHorizon\LivewireHorizon
 */
class LivewireHorizon extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'livewire-horizon';
    }
}
