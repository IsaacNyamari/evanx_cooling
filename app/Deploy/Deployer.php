<?php

namespace App\Deploy;

use App\Models\Deployment;

/**
 * Runs the deployment pipeline for a Deployment record (already claimed as "running"):
 *   git fetch + hard reset to the remote branch, composer install, migrate, seed, rebuild caches.
 * Output is stored on the record as it streams so the admin page can show it live.
 * Every command is fixed (no user input is ever put into a shell command).
 */
class Deployer
{
    public const OPTIONS = ['dependencies', 'migrate', 'seed', 'caches', 'sitemap'];

    private string $output = '';

    private float $lastFlush = 0;

    public function __construct(
        private CommandRunner $runner,
        private ?GitInfo $git = null,
        private ?Environment $environment = null,
    ) {
        $this->git ??= new GitInfo(base_path());
        $this->environment ??= new Environment;
    }

    public function run(Deployment $deployment): bool
    {
        $options = array_merge(array_fill_keys(self::OPTIONS, true), $deployment->options ?? []);
        $branch = $this->git->branch();

        $deployment->update([
            'branch' => $branch,
            'from_commit' => $this->git->head(),
            'output' => '',
        ]);
        $this->output = '';

        $this->log('Deployment #'.$deployment->id.' started '.now()->toDateTimeString()."\n");

        if (! $this->git->isRepo() || $branch === null || ! preg_match('#^[A-Za-z0-9._/-]+$#', $branch)) {
            $this->log("\nThis folder is not a git checkout on a named branch, so there is nothing to pull.\n");

            return $this->finish($deployment, false);
        }

        foreach ($this->steps($branch, $options) as [$label, $command]) {
            $this->log("\n$ {$label}\n");

            $code = $this->runner->run(
                $command,
                base_path(),
                $this->environment->variables(),
                fn (string $chunk) => $this->log($chunk, $deployment),
                (int) config('deploy.step_timeout', 600),
            );

            if ($code !== 0) {
                $this->log("\nStep failed (exit code {$code}). Deployment stopped.\n");

                return $this->finish($deployment, false);
            }
        }

        $this->log("\nAll steps completed.\n");

        return $this->finish($deployment, true);
    }

    /** @return list<array{0:string,1:string}> */
    public function steps(string $branch, array $options): array
    {
        $php = escapeshellarg($this->environment->phpBinary());
        $remote = escapeshellarg((string) config('deploy.remote', 'origin'));
        $remoteBranch = escapeshellarg(config('deploy.remote', 'origin').'/'.$branch);

        $steps = [
            ['git fetch', "git fetch --prune {$remote}"],
            ["git reset --hard {$remote}/{$branch}", "git reset --hard {$remoteBranch}"],
        ];

        if ($options['dependencies']) {
            $steps[] = ['composer install', $this->environment->composerCommand().' install --no-dev --optimize-autoloader --no-interaction --no-progress'];
        }

        if ($options['migrate']) {
            $steps[] = ['php artisan migrate --force', "{$php} artisan migrate --force --no-interaction"];
        }

        if ($options['seed']) {
            $steps[] = ['php artisan db:seed --class=ShopSeeder --force', "{$php} artisan db:seed --class=ShopSeeder --force --no-interaction"];
        }

        if ($options['caches']) {
            $steps[] = ['php artisan optimize:clear', "{$php} artisan optimize:clear"];

            if (app()->isProduction()) {
                $steps[] = ['php artisan config:cache', "{$php} artisan config:cache"];
                $steps[] = ['php artisan view:cache', "{$php} artisan view:cache"];
            }
        }

        if ($options['sitemap']) {
            $steps[] = ['php artisan sitemap:generate', "{$php} artisan sitemap:generate"];
        }

        return $steps;
    }

    private function log(string $text, ?Deployment $deployment = null): void
    {
        $this->output .= $text;

        // Keep the stored log bounded (tail only).
        if (strlen($this->output) > 200_000) {
            $this->output = "[...earlier output trimmed...]\n".substr($this->output, -150_000);
        }

        if ($deployment && microtime(true) - $this->lastFlush > 1.0) {
            $this->flush($deployment);
        }
    }

    private function flush(Deployment $deployment): void
    {
        Deployment::whereKey($deployment->id)->update(['output' => $this->output]);
        $this->lastFlush = microtime(true);
    }

    private function finish(Deployment $deployment, bool $success): bool
    {
        Deployment::whereKey($deployment->id)->update([
            'status' => $success ? 'success' : 'failed',
            'to_commit' => $this->git->head(),
            'output' => $this->output,
            'finished_at' => now(),
        ]);

        return $success;
    }
}
