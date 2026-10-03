<?php

namespace App\Deploy;

/**
 * Works out which php / composer binaries to use and builds the process environment.
 * Web requests on shared hosting often have a bare environment (no HOME, minimal PATH),
 * which breaks git (SSH keys) and composer, so everything is set explicitly.
 */
class Environment
{
    public function home(): string
    {
        $home = getenv('HOME');

        if (! $home && function_exists('posix_getpwuid') && function_exists('posix_geteuid')) {
            $home = posix_getpwuid(posix_geteuid())['dir'] ?? null;
        }

        return $home ?: dirname(base_path());
    }

    public function phpBinary(): string
    {
        if ($configured = config('deploy.php_binary')) {
            return $configured;
        }

        // Under the CLI (cron / background process) PHP_BINARY is the right one.
        if (PHP_SAPI === 'cli' && PHP_BINARY !== '') {
            return PHP_BINARY;
        }

        // Under a web SAPI (lsphp, php-fpm...) look for the matching CLI binary.
        $version = PHP_MAJOR_VERSION.PHP_MINOR_VERSION;
        foreach (["/opt/cpanel/ea-php{$version}/root/usr/bin/php", '/usr/local/bin/php', '/usr/bin/php'] as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return 'php';
    }

    /** The command prefix that runs composer (e.g. "/usr/bin/php /home/user/composer.phar"). */
    public function composerCommand(): string
    {
        if ($configured = config('deploy.composer')) {
            return $configured;
        }

        $php = escapeshellarg($this->phpBinary());

        foreach ([$this->home().'/composer.phar', $this->home().'/bin/composer.phar', '/usr/local/bin/composer', '/usr/bin/composer'] as $candidate) {
            if (is_file($candidate)) {
                return $php.' '.escapeshellarg($candidate);
            }
        }

        return 'composer';
    }

    /** @return array<string,string> */
    public function variables(): array
    {
        $home = $this->home();
        $path = trim((string) getenv('PATH'), ':');
        $path = ($path !== '' ? $path.':' : '').'/usr/local/bin:/usr/bin:/bin';

        $ssh = 'ssh -o BatchMode=yes -o StrictHostKeyChecking=accept-new';
        if ($key = config('deploy.ssh_key')) {
            $ssh .= ' -i '.escapeshellarg($key).' -o IdentitiesOnly=yes';
        }

        return array_merge(getenv() ?: [], [
            'HOME' => $home,
            'PATH' => $path,
            'GIT_TERMINAL_PROMPT' => '0',
            'GIT_SSH_COMMAND' => $ssh,
            'COMPOSER_HOME' => $home.'/.composer',
            'COMPOSER_NO_INTERACTION' => '1',
            'COMPOSER_MEMORY_LIMIT' => '-1',
            'COMPOSER_ALLOW_SUPERUSER' => '1',
        ]);
    }
}
