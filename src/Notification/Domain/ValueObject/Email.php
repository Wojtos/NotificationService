<?php

namespace App\Notification\Domain\ValueObject;

use Assert\Assertion;
use Assert\AssertionFailedException;

final class Email
{
    /** @throws AssertionFailedException */
    public function __construct(
        private readonly string $email
    ) {
        Assertion::email($this->email);
    }

    public function toString(): string
    {
        return $this->email;
    }
}
