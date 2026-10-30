<?php

use Illuminate\Support\Facades\Route;
use JeffersonGoncalves\LivewireHorizon\Livewire;
use Laravel\Horizon\Http\Middleware\Authenticate;

Route::group([
    'domain' => config('livewire-horizon.domain'),
    'prefix' => config('livewire-horizon.path'),
    'middleware' => [...config('livewire-horizon.middleware', ['web']), Authenticate::class],
    'as' => 'livewire-horizon.',
], function () {
    Route::get('/', Livewire\Dashboard::class)->name('dashboard');
    Route::get('/monitoring', Livewire\Monitoring::class)->name('monitoring');
    Route::get('/monitoring/{tag}', Livewire\MonitoringTag::class)->where('tag', '.*')->name('monitoring.tag');
    Route::get('/metrics', Livewire\Metrics::class)->name('metrics');
    Route::get('/metrics/{type}/{id}', Livewire\Metric::class)->whereIn('type', ['jobs', 'queues'])->where('id', '.*')->name('metrics.show');
    Route::get('/batches', Livewire\Batches::class)->name('batches');
    Route::get('/batches/{id}', Livewire\Batch::class)->name('batches.show');
    Route::get('/jobs/{type}', Livewire\Jobs::class)->whereIn('type', ['pending', 'completed', 'silenced'])->name('jobs');
    Route::get('/job/{id}', Livewire\Job::class)->name('jobs.show');
    Route::get('/failed', Livewire\FailedJobs::class)->name('failed');
    Route::get('/failed/{id}', Livewire\FailedJob::class)->name('failed.show');
});
