<?php

namespace App\Notification\Domain\Event;

interface EventBus
{
    public function dispatch(Event $event): void;
}
