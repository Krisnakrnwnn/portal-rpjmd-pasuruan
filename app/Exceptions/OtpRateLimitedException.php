<?php

namespace App\Exceptions;

use RuntimeException;

class OtpRateLimitedException extends RuntimeException
{
    public function __construct(public readonly int $retryAfter)
    {
        parent::__construct('OTP delivery is rate limited.');
    }
}
