<?php

namespace App\Deploy;

use App\Models\Deployment;

interface Launcher
{
    /** Can this server start a background process from a web request at all? */
    public function available(): bool;

    /** Start `artisan deploy:run {id}` in the background. Returns false if it couldn't be started. */
    public function launch(Deployment $deployment): bool;
}
