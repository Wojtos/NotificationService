<?php

namespace App\Notification\Domain\ValueObject;

use Assert\Assertion;
use Assert\AssertionFailedException;

final class DeviceToken
{
    private const TOKEN_LENGTH = 22;

    /** @throws AssertionFailedException */
    public function __construct(
        private readonly string $token
    ) {
        Assertion::length($this->token, self::TOKEN_LENGTH);
    }

    public function toString(): string
    {
        return $this->token;
    }
}
