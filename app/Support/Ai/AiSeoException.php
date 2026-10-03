<?php

namespace App\Support\Ai;

/**
 * A problem generating text that is safe to show to the admin as-is (never contains the API key).
 * "Retryable" problems (quota, unknown model, server trouble) are worth trying the next model for;
 * the others (bad key, blocked content, bad answer) would fail the same way on any model.
 */
class AiSeoException extends \RuntimeException
{
    public function __construct(string $message, public readonly bool $retryable = false)
    {
        parent::__construct($message);
    }
}
