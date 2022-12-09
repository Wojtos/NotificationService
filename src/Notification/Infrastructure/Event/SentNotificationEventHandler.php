<?php

namespace App\Notification\Infrastructure\Event;

use App\Notification\Domain\Event\SentNotificationEvent;
use Psr\Log\LoggerInterface;

use function sprintf;

class SentNotificationEventHandler
{
    public function __construct(
        private readonly LoggerInterface $logger
    ) {
    }

    public function handle(SentNotificationEvent $event): void
    {
        $this->logger->info(sprintf(
            "Successfully sent Notification(id=%s) to User(id=%s) at %s",
            $event->getNotificationId(),
            $event->getUserId(),
            $event->getEventDate()->format('Y-m-d H:i:s')
        ));
    }
}
