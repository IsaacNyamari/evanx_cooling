<?php

namespace App\Livewire\Admin;

use App\Deploy\Access;
use App\Deploy\Deployer;
use App\Deploy\Environment;
use App\Deploy\GitInfo;
use App\Deploy\Launcher;
use App\Models\Deployment;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Deployments extends Component
{
    public string $password = '';

    public bool $dependencies = true;

    public bool $migrate = true;

    public bool $seed = true;

    public bool $caches = true;

    public bool $sitemap = true;

    public ?int $selectedId = null;

    public ?string $notice = null;

    public function mount(): void
    {
        $this->authorizeDeploy();
    }

    public function deploy(Launcher $launcher): void
    {
        $this->authorizeDeploy();

        $this->validate(['password' => ['required', 'string']], ['password.required' => 'Enter your password to confirm.']);

        if (! Hash::check($this->password, auth()->user()->password)) {
            $this->addError('password', 'That password is not correct.');

            return;
        }

        Deployment::releaseStale();

        if (Deployment::active()->exists()) {
            $this->addError('password', 'A deployment is already queued or running. Wait for it to finish.');

            return;
        }

        $deployment = Deployment::create([
            'user_id' => auth()->id(),
            'status' => 'pending',
            'options' => [
                'dependencies' => $this->dependencies,
                'migrate' => $this->migrate,
                'seed' => $this->seed,
                'caches' => $this->caches,
                'sitemap' => $this->sitemap,
            ],
            'output' => "Queued by ".auth()->user()->email."\n",
        ]);

        $this->password = '';
        $this->selectedId = $deployment->id;

        $this->notice = $launcher->launch($deployment)
            ? 'Deployment started. The log below updates live.'
            : 'Deployment queued. This server does not let the website start processes, so it will run within a minute via the scheduled task (see the setup note below).';
    }

    public function select(int $id): void
    {
        $this->authorizeDeploy();
        $this->selectedId = $id;
    }

    private function authorizeDeploy(): void
    {
        abort_unless(config('deploy.enabled'), 404);
        abort_unless(Access::allows(auth()->user()), 403);
    }

    public function render(Launcher $launcher)
    {
        $this->authorizeDeploy();

        $history = Deployment::with('user')->latest('id')->limit(15)->get();
        $selected = $this->selectedId
            ? Deployment::with('user')->find($this->selectedId)
            : $history->first();

        $git = new GitInfo(base_path());
        $environment = new Environment;

        return view('livewire.admin.deployments', [
            'history' => $history,
            'selected' => $selected,
            'active' => Deployment::active()->exists(),
            'branch' => $git->branch(),
            'commit' => $git->head(),
            'canLaunch' => $launcher->available(),
            'cronLine' => '* * * * * cd '.base_path().' && '.$environment->phpBinary().' artisan schedule:run >> /dev/null 2>&1',
            'steps' => (new Deployer(app(\App\Deploy\CommandRunner::class)))->steps($git->branch() ?? 'main', [
                'dependencies' => $this->dependencies, 'migrate' => $this->migrate, 'seed' => $this->seed, 'caches' => $this->caches, 'sitemap' => $this->sitemap,
            ]),
        ]);
    }
}
