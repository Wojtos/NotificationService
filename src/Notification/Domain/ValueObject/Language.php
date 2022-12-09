<?php

namespace App\Notification\Domain\ValueObject;

use Assert\Assertion;

final class Language
{
    public const FALLBACK_LANGUAGE = 'en';
    private const ALLOWED_LANGUAGES = ['en', 'pl'];

    public function __construct(
        private readonly string $language
    ) {
        Assertion::choice($this->language, self::ALLOWED_LANGUAGES);
    }

    public function toString(): string
    {
        return $this->language;
    }
}
