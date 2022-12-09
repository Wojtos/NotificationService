<?php

namespace App\Notification\Infrastructure\Event;

use App\Notification\Domain\Event\Event;
use App\Notification\Domain\Event\EventBus;
use App\Notification\Domain\Event\NotFoundChannelEvent;
use App\Notification\Domain\Event\SentNotificationEvent;

final class SimpleEventBus implements EventBus
{
    public function __construct(
        private readonly SentNotificationEventHandler $sentNotificationEventHandler,
        private readonly NotFoundChannelEventHandler $notFoundChannelEventHandler
    ) {
    }

    public function dispatch(Event $event): void
    {
        match ($event::class) {
            SentNotificationEvent::class => $this->sentNotificationEventHandler->handle($event),
            NotFoundChannelEvent::class => $this->notFoundChannelEventHandler->handle($event),
            default => null
        };
    }
}
