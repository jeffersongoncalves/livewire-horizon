<?php

namespace Workbench\App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use RuntimeException;

class ImportOrders implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $supplier) {}

    public function handle(): void
    {
        throw new RuntimeException("Supplier API for [{$this->supplier}] returned 503.");
    }
}
