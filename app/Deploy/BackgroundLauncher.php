<?php

namespace App\Deploy;

use App\Models\Deployment;

class BackgroundLauncher implements Launcher
{
    private const FUNCTIONS = ['exec', 'shell_exec', 'proc_open', 'popen'];

    public function available(): bool
    {
        return $this->usableFunction() !== null;
    }

    public function launch(Deployment $deployment): bool
    {
        $function = $this->usableFunction();
        if ($function === null) {
            return false;
        }

        $php = escapeshellarg((new Environment)->phpBinary());
        $id = (int) $deployment->id;

        if (PHP_OS_FAMILY === 'Windows') {
            $command = 'start /B "" '.$php.' artisan deploy:run '.$id.' > NUL 2>&1';
        } else {
            $command = 'cd '.escapeshellarg(base_path()).' && nohup '.$php.' artisan deploy:run '.$id.' > /dev/null 2>&1 &';
        }

        try {
            match ($function) {
                'exec' => exec($command),
                'shell_exec' => shell_exec($command),
                'proc_open' => proc_close(proc_open($command, [], $pipes, base_path())),
                'popen' => pclose(popen($command, 'r')),
            };
        } catch (\Throwable) {
            return false;
        }

        return true;
    }

    private function usableFunction(): ?string
    {
        $disabled = array_map('trim', explode(',', (string) ini_get('disable_functions')));

        foreach (self::FUNCTIONS as $function) {
            if (function_exists($function) && ! in_array($function, $disabled, true)) {
                return $function;
            }
        }

        return null;
    }
}
