<?php

namespace App\Notification\Domain\Notifier;

use App\Notification\Domain\Entity\Customer;
use App\Notification\Domain\Entity\Notification;

interface Notifier
{
    public static function getName(): string;
    public function supports(Customer $customer, Notification $notification): bool;
    public function notify(Customer $customer, Notification $notification): bool;
}
