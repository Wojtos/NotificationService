<?php

namespace App\Notification\Domain;

use App\Notification\Domain\Entity\Customer;
use App\Notification\Domain\Entity\Customers;
use App\Notification\Domain\Entity\Notification;
use App\Notification\Domain\Event\EventBus;
use App\Notification\Domain\Event\NotFoundChannelEvent;
use App\Notification\Domain\Event\SentNotificationEvent;
use App\Notification\Domain\Notifier\Notifier;
use App\Notification\Domain\Notifier\NotifierFactoryInterface;

final class NotificationService
{
    /** @var array<Notifier> */
    private array $orderedNotifiers;

    public function __construct(
        private readonly EventBus $eventBus,
        private readonly Clock $clock,
        NotifierFactoryInterface $notifierFactory
    ) {
        $this->orderedNotifiers = $notifierFactory->createAllOrdered();
    }

    public function send(Customers $customers, Notification $notification): void
    {
        foreach ($customers as $customer) {
            $this->sendOne($customer, $notification);
        }
    }

    public function sendOne(Customer $customer, Notification $notification): void
    {
        foreach ($this->orderedNotifiers as $notifier) {
            if (!$notifier->supports($customer, $notification)) {
                continue;
            }
            if ($notifier->notify($customer, $notification)) {
                $this->eventBus->dispatch(new SentNotificationEvent(
                    userId: $customer->getId(),
                    notificationId: $notification->getId(),
                    eventDate: $this->clock->getCurrentDateTime()
                ));
                return;
            }
        }
        $this->eventBus->dispatch(new NotFoundChannelEvent(
            userId: $customer->getId(),
            notificationId: $notification->getId(),
            eventDate: $this->clock->getCurrentDateTime()
        ));
    }
}
