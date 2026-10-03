<?php

namespace App\Console\Commands;

use App\Deploy\Deployer;
use App\Models\Deployment;
use Illuminate\Console\Command;

class DeployRun extends Command
{
    protected $signature = 'deploy:run {deployment : Deployment ID}';

    protected $description = 'Run a queued deployment (started from Admin > Deployments)';

    public function handle(Deployer $deployer): int
    {
        $deployment = Deployment::find($this->argument('deployment'));

        if (! $deployment || ! $deployment->claim()) {
            $this->warn('Deployment not found or already taken.');

            return self::SUCCESS;
        }

        $ok = $deployer->run($deployment);
        $this->line($ok ? 'Deployment succeeded.' : 'Deployment failed.');

        return $ok ? self::SUCCESS : self::FAILURE;
    }
}
