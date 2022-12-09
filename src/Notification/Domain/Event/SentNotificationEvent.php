<?php

namespace App\Notification\Domain\Event;

use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

final class SentNotificationEvent implements Event
{
    public function __construct(
        private readonly UuidInterface $userId,
        private readonly UuidInterface $notificationId,
        private readonly DateTimeInterface $eventDate
    ) {
    }

    public function getUserId(): UuidInterface
    {
        return $this->userId;
    }

    public function getNotificationId(): UuidInterface
    {
        return $this->notificationId;
    }

    public function getEventDate(): DateTimeInterface
    {
        return $this->eventDate;
    }
}
