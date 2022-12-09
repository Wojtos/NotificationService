<?php

namespace App\Notification\Domain\Notifier;

interface NotifierFactoryInterface
{
    public function create(string $name): Notifier;
    /** @return array<Notifier> */
    public function createAllOrdered(): array;
}
