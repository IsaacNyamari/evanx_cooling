<?php

// One-click deployment page (Admin > Deployments). Off unless DEPLOY_ENABLED=true.
return [
    'enabled' => (bool) env('DEPLOY_ENABLED', false),

    // Optional comma-separated list of admin emails allowed to deploy. Empty = any signed-in admin.
    'allowed_emails' => array_values(array_filter(array_map('trim', explode(',', (string) env('DEPLOY_ALLOWED_EMAILS', ''))))),

    // Auto-detected when empty. Set these if the page reports it can't find php/composer.
    'php_binary' => env('DEPLOY_PHP_BINARY'),
    'composer' => env('DEPLOY_COMPOSER'),

    // Optional private SSH key used for `git fetch` (deploy key for a private repository).
    'ssh_key' => env('DEPLOY_SSH_KEY'),

    'remote' => 'origin',

    // Seconds a single step may run, and after which a "running" deployment is considered dead.
    'step_timeout' => 600,
    'stale_after' => 1800,
];
