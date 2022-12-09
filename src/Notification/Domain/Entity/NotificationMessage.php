<?php

namespace App\Notification\Domain\Entity;

use App\Notification\Domain\ValueObject\Language;

final class NotificationMessage
{
    public function __construct(
        private readonly Language $language,
        private readonly string $title,
        private readonly string $text
    ) {
    }

    public function getLanguage(): Language
    {
        return $this->language;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getText(): string
    {
        return $this->text;
    }
}
