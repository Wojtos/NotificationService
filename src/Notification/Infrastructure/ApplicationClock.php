<?php

namespace App\Notification\Infrastructure;

use App\Notification\Domain\Clock;
use DateTimeImmutable;
use DateTimeInterface;

class ApplicationClock implements Clock
{
    public function getCurrentDateTime(): DateTimeInterface
    {
        return new DateTimeImmutable();
    }
}
