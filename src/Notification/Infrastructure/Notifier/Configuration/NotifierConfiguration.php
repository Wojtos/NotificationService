<?php

namespace App\Notification\Infrastructure\Notifier\Configuration;

final class NotifierConfiguration
{
    public function __construct(
        private readonly string $notifierName,
        private readonly bool $enabled,
    ) {
    }

    public function getNotifierName(): string
    {
        return $this->notifierName;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
