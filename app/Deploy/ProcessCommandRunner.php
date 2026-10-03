<?php

namespace App\Deploy;

class ProcessCommandRunner implements CommandRunner
{
    public function run(string $command, string $cwd, array $env, callable $onOutput, int $timeout): int
    {
        $process = @proc_open($command.' 2>&1', [1 => ['pipe', 'w']], $pipes, $cwd, $env);

        if (! is_resource($process)) {
            $onOutput("Could not start the command (is proc_open disabled by the host?).\n");

            return 127;
        }

        stream_set_blocking($pipes[1], false);
        $deadline = time() + $timeout;

        while (true) {
            $chunk = fread($pipes[1], 8192);
            if ($chunk !== false && $chunk !== '') {
                $onOutput($chunk);

                continue;
            }

            $status = proc_get_status($process);
            if (! $status['running']) {
                $rest = stream_get_contents($pipes[1]);
                if ($rest !== false && $rest !== '') {
                    $onOutput($rest);
                }
                fclose($pipes[1]);
                proc_close($process);

                return $status['exitcode'];
            }

            if (time() > $deadline) {
                proc_terminate($process, 9);
                fclose($pipes[1]);
                proc_close($process);
                $onOutput("\nTimed out after {$timeout}s and was stopped.\n");

                return 124;
            }

            usleep(100_000);
        }
    }
}
