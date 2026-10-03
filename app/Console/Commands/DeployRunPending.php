<?php

namespace App\Console\Commands;

use App\Deploy\Deployer;
use App\Models\Deployment;
use Illuminate\Console\Command;

/**
 * Cron fallback for hosts that don't let the website start background processes: the admin page
 * queues the deployment and this command (run every minute by the scheduler) picks it up.
 */
class DeployRunPending extends Command
{
    protected $signature = 'deploy:run-pending';

    protected $description = 'Run queued deployments and clean up deployments that died mid-way';

    public function handle(Deployer $deployer): int
    {
        Deployment::releaseStale();

        foreach (Deployment::where('status', 'pending')->oldest()->get() as $deployment) {
            if ($deployment->claim()) {
                $this->info("Running deployment #{$deployment->id}");
                $deployer->run($deployment);
            }
        }

        return self::SUCCESS;
    }
}
