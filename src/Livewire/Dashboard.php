<?php

namespace JeffersonGoncalves\LivewireHorizon\Livewire;

use Illuminate\Contracts\View\View;
use JeffersonGoncalves\LivewireHorizon\Support\Queues;
use Laravel\Horizon\Contracts\HorizonCommandQueue;
use Laravel\Horizon\Contracts\MasterSupervisorRepository;
use Laravel\Horizon\Contracts\SupervisorRepository;
use Laravel\Horizon\Contracts\WorkloadRepository;
use Laravel\Horizon\Http\Controllers\DashboardStatsController;
use Laravel\Horizon\Http\Controllers\MasterSupervisorController;
use Laravel\Horizon\MasterSupervisor;
use Laravel\Horizon\SupervisorCommands\ContinueWorking;
use Laravel\Horizon\SupervisorCommands\Pause;
use Laravel\Horizon\SupervisorCommands\Restart;

class Dashboard extends Page
{
    /** @var array<string, class-string> */
    protected const COMMANDS = [
        'pause' => Pause::class,
        'continue' => ContinueWorking::class,
        'restart' => Restart::class,
    ];

    /**
     * Pause, continue or restart a master supervisor (all its supervisors) or a
     * single supervisor, through Horizon's command queue: it works across
     * servers and users, unlike the signals sent by `horizon:pause`.
     */
    public function control(string $action, string $target, string $name): void
    {
        $command = self::COMMANDS[$action] ?? abort(422);

        $exists = $target === 'master'
            ? app(MasterSupervisorRepository::class)->find($name)
            : app(SupervisorRepository::class)->find($name);

        abort_unless((bool) $exists, 404);

        app(HorizonCommandQueue::class)->push(
            $target === 'master' ? MasterSupervisor::commandQueueFor($name) : $name,
            $command,
        );

        $this->toast(ucfirst($action)." sent to {$name}. It takes effect on the next supervisor loop.");
    }

    public function clearQueue(string $queue): void
    {
        $count = Queues::clear($queue);

        $this->toast("Cleared {$count} jobs from the [{$queue}] queue.");
    }

    public function render(): View
    {
        return $this->page('dashboard', 'Dashboard', [
            'stats' => app(DashboardStatsController::class)->index(),
            'workload' => collect(app(WorkloadRepository::class)->get())->sortBy('name')->values(),
            'masters' => app(MasterSupervisorController::class)->index(
                app(MasterSupervisorRepository::class),
                app(SupervisorRepository::class),
            ),
        ]);
    }
}
