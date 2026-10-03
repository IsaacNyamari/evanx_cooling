<?php

namespace Tests\Feature;

use App\Deploy\CommandRunner;
use App\Deploy\Deployer;
use App\Deploy\GitInfo;
use App\Deploy\Launcher;
use App\Livewire\Admin\Deployments;
use App\Models\Deployment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FakeRunner implements CommandRunner
{
    public array $commands = [];

    public function __construct(private ?string $failOn = null) {}

    public function run(string $command, string $cwd, array $env, callable $onOutput, int $timeout): int
    {
        $this->commands[] = $command;
        $onOutput("output of: {$command}\n");

        return $this->failOn && str_contains($command, $this->failOn) ? 1 : 0;
    }
}

class FakeLauncher implements Launcher
{
    public array $launched = [];

    public function __construct(private bool $available = true) {}

    public function available(): bool
    {
        return $this->available;
    }

    public function launch(Deployment $deployment): bool
    {
        $this->launched[] = $deployment->id;

        return $this->available;
    }
}

class StubGit extends GitInfo
{
    public function __construct(private ?string $branch = 'main') {}

    public function isRepo(): bool
    {
        return $this->branch !== null;
    }

    public function branch(): ?string
    {
        return $this->branch;
    }

    public function head(): ?string
    {
        return str_repeat('a', 40);
    }
}

class DeploymentTest extends TestCase
{
    use RefreshDatabase;

    private function pending(array $options = []): Deployment
    {
        $d = Deployment::create(['status' => 'pending', 'options' => $options]);
        $this->assertTrue($d->claim());

        return $d;
    }

    private function admin(): User
    {
        return User::factory()->create(['password' => bcrypt('correct-horse')]);
    }

    public function test_pipeline_runs_every_step_in_order_and_records_success(): void
    {
        $runner = new FakeRunner;
        $d = $this->pending();

        $this->assertTrue((new Deployer($runner, new StubGit))->run($d));

        $joined = implode("\n", $runner->commands);
        $this->assertStringContainsString('git fetch', $runner->commands[0]);
        $this->assertStringContainsString('git reset --hard', $runner->commands[1]);
        $this->assertStringContainsString("origin/main", $runner->commands[1]);
        $this->assertStringContainsString('install --no-dev', $joined);
        $this->assertStringContainsString('artisan migrate --force', $joined);
        $this->assertStringContainsString('db:seed --class=ShopSeeder --force', $joined);
        $this->assertStringContainsString('optimize:clear', $joined);
        $this->assertLessThan(strpos($joined, 'migrate'), strpos($joined, 'install --no-dev'));

        $d->refresh();
        $this->assertSame('success', $d->status);
        $this->assertSame('main', $d->branch);
        $this->assertNotNull($d->finished_at);
        $this->assertStringContainsString('All steps completed', $d->output);
    }

    public function test_a_failing_step_stops_the_pipeline(): void
    {
        $runner = new FakeRunner(failOn: 'migrate');
        $d = $this->pending();

        $this->assertFalse((new Deployer($runner, new StubGit))->run($d));

        $joined = implode("\n", $runner->commands);
        $this->assertStringNotContainsString('db:seed', $joined);
        $this->assertSame('failed', $d->fresh()->status);
        $this->assertStringContainsString('Deployment stopped', $d->fresh()->output);
    }

    public function test_options_skip_steps(): void
    {
        $runner = new FakeRunner;
        $d = $this->pending(['dependencies' => false, 'migrate' => false, 'seed' => false, 'caches' => false, 'sitemap' => false]);

        (new Deployer($runner, new StubGit))->run($d);

        $this->assertCount(2, $runner->commands); // only git fetch + reset
    }

    public function test_refuses_when_not_a_git_checkout(): void
    {
        $runner = new FakeRunner;
        $d = $this->pending();

        $this->assertFalse((new Deployer($runner, new StubGit(null)))->run($d));
        $this->assertSame([], $runner->commands);
        $this->assertSame('failed', $d->fresh()->status);
    }

    public function test_page_is_hidden_unless_enabled_and_allowed(): void
    {
        $user = $this->admin();

        config(['deploy.enabled' => false]);
        $this->actingAs($user)->get('/admin/deployments')->assertNotFound();
        $this->get('/admin')->assertDontSee('Deployments');

        config(['deploy.enabled' => true, 'deploy.allowed_emails' => ['someone-else@example.com']]);
        $this->get('/admin/deployments')->assertForbidden();

        config(['deploy.allowed_emails' => [strtoupper($user->email)]]);
        $this->get('/admin/deployments')->assertOk()->assertSee('Deploy now');
        $this->get('/admin')->assertSee('Deployments');
    }

    public function test_guests_cannot_reach_the_page(): void
    {
        config(['deploy.enabled' => true]);
        $this->get('/admin/deployments')->assertRedirect('/login');
    }

    public function test_deploy_requires_the_correct_password(): void
    {
        config(['deploy.enabled' => true]);
        $launcher = new FakeLauncher;
        $this->app->instance(Launcher::class, $launcher);

        Livewire::actingAs($this->admin())->test(Deployments::class)
            ->set('password', 'wrong')->call('deploy')->assertHasErrors('password')
            ->set('password', '')->call('deploy')->assertHasErrors('password');

        $this->assertSame(0, Deployment::count());
        $this->assertSame([], $launcher->launched);
    }

    public function test_deploy_queues_launches_and_blocks_a_second_run(): void
    {
        config(['deploy.enabled' => true]);
        $launcher = new FakeLauncher;
        $this->app->instance(Launcher::class, $launcher);

        $component = Livewire::actingAs($this->admin())->test(Deployments::class)
            ->set('seed', false)
            ->set('password', 'correct-horse')->call('deploy')->assertHasNoErrors();

        $deployment = Deployment::first();
        $this->assertSame('pending', $deployment->status);
        $this->assertFalse($deployment->options['seed']);
        $this->assertSame([$deployment->id], $launcher->launched);

        $component->set('password', 'correct-horse')->call('deploy')->assertHasErrors('password');
        $this->assertSame(1, Deployment::count());
    }

    public function test_falls_back_to_cron_when_the_host_blocks_background_processes(): void
    {
        config(['deploy.enabled' => true]);
        $this->app->instance(Launcher::class, new FakeLauncher(available: false));

        Livewire::actingAs($this->admin())->test(Deployments::class)
            ->set('password', 'correct-horse')->call('deploy')
            ->assertSee('schedule:run')
            ->assertSee('scheduled task');

        $this->assertSame('pending', Deployment::first()->status);
    }

    public function test_run_pending_command_runs_queued_deployments_and_frees_stale_locks(): void
    {
        $runner = new FakeRunner;
        $this->app->instance(CommandRunner::class, $runner);
        $this->app->bind(Deployer::class, fn () => new Deployer($runner, new StubGit));

        $stale = Deployment::create(['status' => 'running', 'started_at' => now()->subHours(2)]);
        $queued = Deployment::create(['status' => 'pending']);

        $this->artisan('deploy:run-pending')->assertSuccessful();

        $this->assertSame('failed', $stale->fresh()->status);
        $this->assertSame('success', $queued->fresh()->status);
        $this->assertNotEmpty($runner->commands);
    }

    public function test_a_deployment_can_only_be_claimed_once(): void
    {
        $d = Deployment::create(['status' => 'pending']);

        $this->assertTrue($d->claim());
        $this->assertFalse(Deployment::find($d->id)->claim());
    }

    public function test_real_process_runner_streams_output_and_reports_exit_codes(): void
    {
        $runner = new \App\Deploy\ProcessCommandRunner;
        $out = '';

        $code = $runner->run('echo deploy-ok', base_path(), getenv() ?: [], function ($c) use (&$out) { $out .= $c; }, 30);
        $this->assertSame(0, $code);
        $this->assertStringContainsString('deploy-ok', $out);

        $code = $runner->run('php -r "fwrite(STDERR, \'oops\'); exit(3);"', base_path(), getenv() ?: [], function ($c) use (&$out) { $out .= $c; }, 30);
        $this->assertSame(3, $code);
        $this->assertStringContainsString('oops', $out);
    }

    public function test_environment_detects_binaries_and_sets_a_safe_process_env(): void
    {
        $env = new \App\Deploy\Environment;

        $this->assertNotSame('', $env->phpBinary());
        $this->assertNotSame('', $env->composerCommand());

        $vars = $env->variables();
        $this->assertSame('0', $vars['GIT_TERMINAL_PROMPT']);
        $this->assertNotEmpty($vars['HOME']);
        $this->assertStringContainsString('BatchMode=yes', $vars['GIT_SSH_COMMAND']);

        config(['deploy.ssh_key' => '/home/u/.ssh/deploy_key']);
        $this->assertStringContainsString('IdentitiesOnly=yes', $env->variables()['GIT_SSH_COMMAND']);
        $this->assertStringContainsString('deploy_key', $env->variables()['GIT_SSH_COMMAND']);
    }

    public function test_git_info_reads_branch_and_commit_without_git_binary(): void
    {
        $root = sys_get_temp_dir().'/gi_'.uniqid();
        mkdir($root.'/.git/refs/heads', 0777, true);
        file_put_contents($root.'/.git/HEAD', "ref: refs/heads/main\n");
        file_put_contents($root.'/.git/refs/heads/main', str_repeat('b', 40)."\n");

        $git = new GitInfo($root);
        $this->assertSame('main', $git->branch());
        $this->assertSame(str_repeat('b', 40), $git->head());

        unlink($root.'/.git/refs/heads/main');
        file_put_contents($root.'/.git/packed-refs', str_repeat('c', 40)." refs/heads/main\n");
        $this->assertSame(str_repeat('c', 40), $git->head());

        $this->assertNull((new GitInfo($root.'/nope'))->branch());
    }
}
