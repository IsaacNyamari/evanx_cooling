<?php

namespace App\Deploy;

/** Reads the checked-out branch and commit straight from .git, so it works even if exec() is disabled. */
class GitInfo
{
    public function __construct(private string $root) {}

    public function isRepo(): bool
    {
        return is_file($this->root.'/.git/HEAD');
    }

    /** Current branch name, or null when not a repo / detached HEAD. */
    public function branch(): ?string
    {
        if (! $this->isRepo()) {
            return null;
        }

        $head = trim((string) @file_get_contents($this->root.'/.git/HEAD'));

        return str_starts_with($head, 'ref: refs/heads/') ? substr($head, 16) : null;
    }

    public function head(): ?string
    {
        if (! $this->isRepo()) {
            return null;
        }

        $head = trim((string) @file_get_contents($this->root.'/.git/HEAD'));

        if (! str_starts_with($head, 'ref: ')) {
            return $head ?: null;
        }

        $ref = substr($head, 5);
        $file = $this->root.'/.git/'.$ref;
        if (is_file($file)) {
            return trim((string) file_get_contents($file)) ?: null;
        }

        $packed = @file_get_contents($this->root.'/.git/packed-refs') ?: '';
        if (preg_match('/^([0-9a-f]{40}) '.preg_quote($ref, '/').'$/m', $packed, $m)) {
            return $m[1];
        }

        return null;
    }
}
