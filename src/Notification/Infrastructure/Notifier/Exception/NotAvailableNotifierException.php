<?php

namespace App\Notification\Infrastructure\Notifier\Exception;

use Exception;

class NotAvailableNotifierException extends Exception
{
    public function __construct(string $notifierName)
    {
        parent::__construct("Notifier(name=$notifierName) is not available!");
    }
}
