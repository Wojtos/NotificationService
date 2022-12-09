<?php

namespace App\Notification\Domain\Entity;

use App\Notification\Domain\ValueObject\Language;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class Notification
{
    /** @param array<string, NotificationMessage> $messagesByLanguage */
    private function __construct(
        private readonly UuidInterface $id,
        private readonly array $messagesByLanguage,
        private readonly Language $fallBackLanguage,
    ) {
    }

    /** @param array<string, NotificationMessage> $messagesByLanguage */
    public static function createNew(
        array $messagesByLanguage,
        Language $fallBackLanguage,
    ): self {
        return new self(
            id: Uuid::uuid4(),
            messagesByLanguage: $messagesByLanguage,
            fallBackLanguage: $fallBackLanguage
        );
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getMessage(Language $language): NotificationMessage
    {
        return $this->messagesByLanguage[$language->toString()] ??
            $this->messagesByLanguage[$this->fallBackLanguage->toString()];
    }
}
