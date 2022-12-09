<?php

namespace App\Notification\Domain;

use DateTimeInterface;

interface Clock
{
    public function getCurrentDateTime(): DateTimeInterface;
}
