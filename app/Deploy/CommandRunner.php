<?php

namespace App\Deploy;

interface CommandRunner
{
    /**
     * Run a shell command, streaming combined stdout/stderr to $onOutput. Returns the exit code.
     *
     * @param  array<string,string>  $env
     * @param  callable(string):void  $onOutput
     */
    public function run(string $command, string $cwd, array $env, callable $onOutput, int $timeout): int;
}
