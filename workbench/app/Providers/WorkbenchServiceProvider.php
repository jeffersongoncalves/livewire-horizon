<?php

namespace Workbench\App\Providers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;
use Laravel\Horizon\Contracts\MetricsRepository;
use Laravel\Horizon\Horizon;
use Workbench\App\Jobs\ImportOrders;
use Workbench\App\Jobs\SendInvoice;

/**
 * Local demo: `composer serve` after `composer demo` (needs Redis).
 */
class WorkbenchServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Horizon::auth(fn () => true);

        Artisan::command('demo:seed', function () {
            foreach (range(1, 40) as $i) {
                SendInvoice::dispatch($i)->onQueue($i % 3 === 0 ? 'mail' : 'default');
            }

            foreach (range(1, 12) as $i) {
                ImportOrders::dispatch("supplier-{$i}")->onQueue('imports');
            }

            $this->call('queue:work', ['connection' => 'redis', '--queue' => 'default,mail,imports', '--stop-when-empty' => true, '--tries' => 1]);

            foreach (range(41, 55) as $i) {
                SendInvoice::dispatch($i);
            }

            // Not `horizon:snapshot`: its lock allows one snapshot every ~5 minutes.
            app(MetricsRepository::class)->snapshot();
        });
    }
}
