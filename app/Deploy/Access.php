<?php

namespace App\Deploy;

use App\Models\User;

class Access
{
    /** Deployments are off by default and can be limited to specific admin emails. */
    public static function allows(?User $user): bool
    {
        if (! $user || ! config('deploy.enabled')) {
            return false;
        }

        $allowed = array_map('strtolower', config('deploy.allowed_emails', []));

        return $allowed === [] || in_array(strtolower($user->email), $allowed, true);
    }
}
