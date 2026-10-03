<?php

namespace App\Providers;

use App\Deploy\BackgroundLauncher;
use App\Deploy\CommandRunner;
use App\Deploy\Launcher;
use App\Deploy\ProcessCommandRunner;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CommandRunner::class, ProcessCommandRunner::class);
        $this->app->bind(Launcher::class, BackgroundLauncher::class);
        $this->app->scoped(\App\Support\Seo::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
    }
}
