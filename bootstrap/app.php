<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Expired CSRF token (plain form posts): send people back with a friendly note.
        // Livewire handles its own 419s, and JSON clients still get the status code.
        $exceptions->render(function (HttpException $e, Request $request) {
            if ($e->getStatusCode() !== 419 || $request->expectsJson() || $request->hasHeader('X-Livewire')) {
                return null;
            }

            return redirect()->back(fallback: route('home'))
                ->withInput($request->except('password', 'password_confirmation'))
                ->with('error', 'Your session expired. Please try again.');
        });

        // Visiting a POST-only URL (e.g. /send-message) in the browser: go somewhere useful.
        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) {
            if (! $request->isMethod('GET') || $request->expectsJson()) {
                return null;
            }

            return redirect()->route($request->is('send-message*') ? 'contact' : 'home');
        });

        // Signed-out visitors poking at non-existent admin URLs go to the login page.
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('admin', 'admin/*') && $request->user() === null && ! $request->expectsJson()) {
                return redirect()->guest(route('login'));
            }

            return null;
        });
    })->create();
