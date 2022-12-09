<?php

namespace App\Notification\Domain\ValueObject;

use Assert\Assertion;
use Assert\AssertionFailedException;

final class PhoneNumber
{
    /** @throws AssertionFailedException */
    public function __construct(
        private readonly string $phoneNumber
    ) {
        Assertion::e164($this->phoneNumber);
    }

    public function toString(): string
    {
        return $this->phoneNumber;
    }
}
