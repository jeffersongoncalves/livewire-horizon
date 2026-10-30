<?php

namespace JeffersonGoncalves\LivewireHorizon\Livewire;

use Flux\FluxManager;
use Illuminate\Contracts\View\View;
use Laravel\Horizon\Horizon;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Base for every dashboard page: full-page layout + Horizon's access gate,
 * checked on every Livewire request (not only the first page load).
 */
#[Layout('livewire-horizon::layout')]
abstract class Page extends Component
{
    public function boot(): void
    {
        abort_unless(Horizon::check(request()), 403);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function page(string $view, string $title, array $data = []): View
    {
        view()->share('livewireHorizonTitle', $title);

        return view("livewire-horizon::livewire.{$view}", $data);
    }

    /**
     * Called by the auto-load timer (partials/auto-load): a named no-op that just
     * re-renders, so wire:loading can tell it apart from what the user does.
     */
    public function autoLoad(): void {}

    protected function toast(string $message, string $variant = 'success'): void
    {
        app(FluxManager::class)->toast($message, variant: $variant);
    }
}
