<?php

namespace App\Notification\Infrastructure\Event;

use App\Notification\Domain\Event\NotFoundChannelEvent;
use Psr\Log\LoggerInterface;

class NotFoundChannelEventHandler
{
    public function __construct(
        private readonly LoggerInterface $logger
    ) {
    }

    public function handle(NotFoundChannelEvent $event): void
    {
        $this->logger->info(sprintf(
            "Could not find a valid channel for Notification(id=%s) to User(id=%s) at %s",
            $event->getNotificationId(),
            $event->getUserId(),
            $event->getEventDate()->format('Y-m-d H:i:s')
        ));
    }
}
