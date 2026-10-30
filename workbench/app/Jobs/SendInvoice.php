<?php

namespace Workbench\App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendInvoice implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $invoice) {}

    public function handle(): void
    {
        usleep(random_int(10, 200) * 1000);
    }

    /**
     * @return array<int, string>
     */
    public function tags(): array
    {
        return ["invoice:{$this->invoice}", 'customer:'.($this->invoice % 5)];
    }
}
