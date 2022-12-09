<?php

namespace App\Tests\Notification\Domain\ValueObject;

use App\Notification\Domain\Clock;
use App\Notification\Domain\Entity\Customer;
use App\Notification\Domain\Entity\Customers;
use App\Notification\Domain\Entity\Notification;
use App\Notification\Domain\Entity\NotificationMessage;
use App\Notification\Domain\Event\EventBus;
use App\Notification\Domain\Event\NotFoundChannelEvent;
use App\Notification\Domain\Event\SentNotificationEvent;
use App\Notification\Domain\NotificationService;
use App\Notification\Domain\Notifier\Notifier;
use App\Notification\Domain\Notifier\NotifierFactoryInterface;
use App\Notification\Domain\ValueObject\DeviceToken;
use App\Notification\Domain\ValueObject\Email;
use App\Notification\Domain\ValueObject\Language;
use App\Notification\Domain\ValueObject\PhoneNumber;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class NotificationServiceTest extends TestCase
{
    public function testSendOneNoneActive(): void
    {
        $currentDateTime = new DateTimeImmutable('2022-01-01');
        $clockMock = $this->createMock(Clock::class);
        $clockMock
            ->expects($this->once())
            ->method('getCurrentDateTime')
            ->with()
            ->willReturn($currentDateTime);

        $exampleLanguage = new Language('en');
        $customer = Customer::createNew(
            language: $exampleLanguage,
            email: new Email('test@test.com'),
            phoneNumber: new PhoneNumber('123123123'),
            deviceToken: new DeviceToken('a6345d0278adc55d3474f5')
        );
        $notification = Notification::createNew(
            [
                $exampleLanguage->toString() => new NotificationMessage(
                    language: $exampleLanguage,
                    title: 'example_title',
                    text: 'example_text'
                )
            ],
            $exampleLanguage
        );

        $eventBusMock = $this->createMock(EventBus::class);
        $eventBusMock
            ->expects($this->once())
            ->method('dispatch')
            ->with(new NotFoundChannelEvent(
                userId: $customer->getId(),
                notificationId: $notification->getId(),
                eventDate: $currentDateTime
        ));

        $notifierFactoryMock = $this->createMock(NotifierFactoryInterface::class);
        $notifierFactoryMock->expects($this->once())
            ->method('createAllOrdered')
            ->with()
            ->willReturn([]);

        $service = new NotificationService(
            eventBus: $eventBusMock,
            clock: $clockMock,
            notifierFactory: $notifierFactoryMock
        );
        $service->sendOne($customer, $notification);
    }

    public function testSendOneNotSupported(): void
    {
        $currentDateTime = new DateTimeImmutable('2022-01-01');
        $clockMock = $this->createMock(Clock::class);
        $clockMock
            ->expects($this->once())
            ->method('getCurrentDateTime')
            ->with()
            ->willReturn($currentDateTime);

        $exampleLanguage = new Language('en');
        $customer = Customer::createNew(
            language: $exampleLanguage,
            email: new Email('test@test.com'),
            phoneNumber: new PhoneNumber('123123123'),
            deviceToken: new DeviceToken('a6345d0278adc55d3474f5')
        );
        $notification = Notification::createNew(
            [
                $exampleLanguage->toString() => new NotificationMessage(
                    language: $exampleLanguage,
                    title: 'example_title',
                    text: 'example_text'
                )
            ],
            $exampleLanguage
        );

        $eventBusMock = $this->createMock(EventBus::class);
        $eventBusMock
            ->expects($this->once())
            ->method('dispatch')
            ->with(new NotFoundChannelEvent(
                userId: $customer->getId(),
                notificationId: $notification->getId(),
                eventDate: $currentDateTime
        ));

        $notifierMock = $this->createMock(Notifier::class);
        $notifierMock
            ->expects($this->once())
            ->method('supports')
            ->with($customer, $notification)
            ->willReturn(false);
        $notifierMock
            ->expects($this->never())
            ->method('notify');

        $notifierFactoryMock = $this->createMock(NotifierFactoryInterface::class);
        $notifierFactoryMock->expects($this->once())
            ->method('createAllOrdered')
            ->with()
            ->willReturn([$notifierMock]);

        $service = new NotificationService(
            eventBus: $eventBusMock,
            clock: $clockMock,
            notifierFactory: $notifierFactoryMock
        );
        $service->sendOne($customer, $notification);
    }

    public function testSendOneUnsuccessfulRequest(): void
    {
        $currentDateTime = new DateTimeImmutable('2022-01-01');
        $clockMock = $this->createMock(Clock::class);
        $clockMock
            ->expects($this->once())
            ->method('getCurrentDateTime')
            ->with()
            ->willReturn($currentDateTime);

        $exampleLanguage = new Language('en');
        $customer = Customer::createNew(
            language: $exampleLanguage,
            email: new Email('test@test.com'),
            phoneNumber: new PhoneNumber('123123123'),
            deviceToken: new DeviceToken('a6345d0278adc55d3474f5')
        );
        $notification = Notification::createNew(
            [
                $exampleLanguage->toString() => new NotificationMessage(
                    language: $exampleLanguage,
                    title: 'example_title',
                    text: 'example_text'
                )
            ],
            $exampleLanguage
        );

        $eventBusMock = $this->createMock(EventBus::class);
        $eventBusMock
            ->expects($this->once())
            ->method('dispatch')
            ->with(new NotFoundChannelEvent(
                userId: $customer->getId(),
                notificationId: $notification->getId(),
                eventDate: $currentDateTime
        ));

        $notifierMock = $this->createMock(Notifier::class);
        $notifierMock
            ->expects($this->once())
            ->method('supports')
            ->with($customer, $notification)
            ->willReturn(true);
        $notifierMock
            ->expects($this->once())
            ->method('notify')
            ->with($customer, $notification)
            ->willReturn(false);

        $notifierFactoryMock = $this->createMock(NotifierFactoryInterface::class);
        $notifierFactoryMock->expects($this->once())
            ->method('createAllOrdered')
            ->with()
            ->willReturn([$notifierMock]);

        $service = new NotificationService(
            eventBus: $eventBusMock,
            clock: $clockMock,
            notifierFactory: $notifierFactoryMock
        );
        $service->sendOne($customer, $notification);
    }

    public function testSendOneSuccessfulRequest(): void
    {
        $currentDateTime = new DateTimeImmutable('2022-01-01');
        $clockMock = $this->createMock(Clock::class);
        $clockMock
            ->expects($this->once())
            ->method('getCurrentDateTime')
            ->with()
            ->willReturn($currentDateTime);

        $exampleLanguage = new Language('en');
        $customer = Customer::createNew(
            language: $exampleLanguage,
            email: new Email('test@test.com'),
            phoneNumber: new PhoneNumber('123123123'),
            deviceToken: new DeviceToken('a6345d0278adc55d3474f5')
        );
        $notification = Notification::createNew(
            [
                $exampleLanguage->toString() => new NotificationMessage(
                    language: $exampleLanguage,
                    title: 'example_title',
                    text: 'example_text'
                )
            ],
            $exampleLanguage
        );

        $eventBusMock = $this->createMock(EventBus::class);
        $eventBusMock
            ->expects($this->once())
            ->method('dispatch')
            ->with(new SentNotificationEvent(
                userId: $customer->getId(),
                notificationId: $notification->getId(),
                eventDate: $currentDateTime
        ));

        $notifierMock = $this->createMock(Notifier::class);
        $notifierMock
            ->expects($this->once())
            ->method('supports')
            ->with($customer, $notification)
            ->willReturn(true);
        $notifierMock
            ->expects($this->once())
            ->method('notify')
            ->with($customer, $notification)
            ->willReturn(true);

        $notifierFactoryMock = $this->createMock(NotifierFactoryInterface::class);
        $notifierFactoryMock->expects($this->once())
            ->method('createAllOrdered')
            ->with()
            ->willReturn([$notifierMock]);

        $service = new NotificationService(
            eventBus: $eventBusMock,
            clock: $clockMock,
            notifierFactory: $notifierFactoryMock
        );
        $service->sendOne($customer, $notification);
    }

    public function testSendOneNotSupportedTwoNotifiers(): void
    {
        $currentDateTime = new DateTimeImmutable('2022-01-01');
        $clockMock = $this->createMock(Clock::class);
        $clockMock
            ->expects($this->once())
            ->method('getCurrentDateTime')
            ->with()
            ->willReturn($currentDateTime);

        $exampleLanguage = new Language('en');
        $customer = Customer::createNew(
            language: $exampleLanguage,
            email: new Email('test@test.com'),
            phoneNumber: new PhoneNumber('123123123'),
            deviceToken: new DeviceToken('a6345d0278adc55d3474f5')
        );
        $notification = Notification::createNew(
            [
                $exampleLanguage->toString() => new NotificationMessage(
                    language: $exampleLanguage,
                    title: 'example_title',
                    text: 'example_text'
                )
            ],
            $exampleLanguage
        );

        $eventBusMock = $this->createMock(EventBus::class);
        $eventBusMock
            ->expects($this->once())
            ->method('dispatch')
            ->with(new NotFoundChannelEvent(
                userId: $customer->getId(),
                notificationId: $notification->getId(),
                eventDate: $currentDateTime
            ));

        $firstNotifierMock = $this->createMock(Notifier::class);
        $firstNotifierMock
            ->expects($this->once())
            ->method('supports')
            ->with($customer, $notification)
            ->willReturn(false);
        $firstNotifierMock
            ->expects($this->never())
            ->method('notify');

        $secondNotifierMock = $this->createMock(Notifier::class);
        $secondNotifierMock
            ->expects($this->once())
            ->method('supports')
            ->with($customer, $notification)
            ->willReturn(false);
        $secondNotifierMock
            ->expects($this->never())
            ->method('notify');

        $notifierFactoryMock = $this->createMock(NotifierFactoryInterface::class);
        $notifierFactoryMock->expects($this->once())
            ->method('createAllOrdered')
            ->with()
            ->willReturn([$firstNotifierMock, $secondNotifierMock]);

        $service = new NotificationService(
            eventBus: $eventBusMock,
            clock: $clockMock,
            notifierFactory: $notifierFactoryMock
        );
        $service->sendOne($customer, $notification);
    }

    public function testSendOneNotSupportedFirstNotifierAndSuccessfulSecondNotifier(): void
    {
        $currentDateTime = new DateTimeImmutable('2022-01-01');
        $clockMock = $this->createMock(Clock::class);
        $clockMock
            ->expects($this->once())
            ->method('getCurrentDateTime')
            ->with()
            ->willReturn($currentDateTime);

        $exampleLanguage = new Language('en');
        $customer = Customer::createNew(
            language: $exampleLanguage,
            email: new Email('test@test.com'),
            phoneNumber: new PhoneNumber('123123123'),
            deviceToken: new DeviceToken('a6345d0278adc55d3474f5')
        );
        $notification = Notification::createNew(
            [
                $exampleLanguage->toString() => new NotificationMessage(
                    language: $exampleLanguage,
                    title: 'example_title',
                    text: 'example_text'
                )
            ],
            $exampleLanguage
        );

        $eventBusMock = $this->createMock(EventBus::class);
        $eventBusMock
            ->expects($this->once())
            ->method('dispatch')
            ->with(new SentNotificationEvent(
                userId: $customer->getId(),
                notificationId: $notification->getId(),
                eventDate: $currentDateTime
            ));

        $firstNotifierMock = $this->createMock(Notifier::class);
        $firstNotifierMock
            ->expects($this->once())
            ->method('supports')
            ->with($customer, $notification)
            ->willReturn(false);
        $firstNotifierMock
            ->expects($this->never())
            ->method('notify');

        $secondNotifierMock = $this->createMock(Notifier::class);
        $secondNotifierMock
            ->expects($this->once())
            ->method('supports')
            ->with($customer, $notification)
            ->willReturn(true);
        $secondNotifierMock
            ->expects($this->once())
            ->method('notify')
            ->with($customer, $notification)
            ->willReturn(true);

        $notifierFactoryMock = $this->createMock(NotifierFactoryInterface::class);
        $notifierFactoryMock->expects($this->once())
            ->method('createAllOrdered')
            ->with()
            ->willReturn([$firstNotifierMock, $secondNotifierMock]);

        $service = new NotificationService(
            eventBus: $eventBusMock,
            clock: $clockMock,
            notifierFactory: $notifierFactoryMock
        );
        $service->sendOne($customer, $notification);
    }

    public function testSendEmptyCustomers(): void
    {
        $clockMock = $this->createMock(Clock::class);
        $clockMock
            ->expects($this->never())
            ->method('getCurrentDateTime');

        $exampleLanguage = new Language('en');
        $notification = Notification::createNew(
            [
                $exampleLanguage->toString() => new NotificationMessage(
                    language: $exampleLanguage,
                    title: 'example_title',
                    text: 'example_text'
                )
            ],
            $exampleLanguage
        );

        $eventBusMock = $this->createMock(EventBus::class);
        $eventBusMock
            ->expects($this->never())
            ->method('dispatch');

        $firstNotifierMock = $this->createMock(Notifier::class);
        $firstNotifierMock
            ->expects($this->never())
            ->method('supports');
        $firstNotifierMock
            ->expects($this->never())
            ->method('notify');

        $notifierFactoryMock = $this->createMock(NotifierFactoryInterface::class);
        $notifierFactoryMock->expects($this->once())
            ->method('createAllOrdered')
            ->with()
            ->willReturn([$firstNotifierMock]);

        $service = new NotificationService(
            eventBus: $eventBusMock,
            clock: $clockMock,
            notifierFactory: $notifierFactoryMock
        );
        $service->send(new Customers([]), $notification);
    }

    public function testSendTwoCustomersBothNotSupported(): void
    {
        $currentDateTime = new DateTimeImmutable('2022-01-01');
        $clockMock = $this->createMock(Clock::class);
        $clockMock
            ->expects($this->exactly(2))
            ->method('getCurrentDateTime')
            ->with()
            ->willReturn($currentDateTime);

        $exampleLanguage = new Language('en');
        $firstCustomer = Customer::createNew(
            language: $exampleLanguage,
            email: new Email('test@test.com'),
            phoneNumber: new PhoneNumber('123123123'),
            deviceToken: new DeviceToken('a6345d0278adc55d3474f5')
        );
        $secondCustomer = Customer::createNew(
            language: $exampleLanguage,
            email: new Email('test2@test.com'),
            phoneNumber: new PhoneNumber('123123121'),
            deviceToken: new DeviceToken('a6345d0278adc55d3474fa')
        );
        $notification = Notification::createNew(
            [
                $exampleLanguage->toString() => new NotificationMessage(
                    language: $exampleLanguage,
                    title: 'example_title',
                    text: 'example_text'
                )
            ],
            $exampleLanguage
        );

        $eventBusMock = $this->createMock(EventBus::class);
        $eventBusMock
            ->expects($this->exactly(2))
            ->method('dispatch')
            ->withConsecutive(
                [new NotFoundChannelEvent(
                    userId: $firstCustomer->getId(),
                    notificationId: $notification->getId(),
                    eventDate: $currentDateTime
                )],
                [new NotFoundChannelEvent(
                    userId: $secondCustomer->getId(),
                    notificationId: $notification->getId(),
                    eventDate: $currentDateTime
                )]
            );

        $firstNotifierMock = $this->createMock(Notifier::class);
        $firstNotifierMock
            ->expects($this->exactly(2))
            ->method('supports')
            ->withConsecutive(
                [$firstCustomer, $notification],
                [$secondCustomer, $notification]
            )
            ->willReturnOnConsecutiveCalls(false, false);
        $firstNotifierMock
            ->expects($this->never())
            ->method('notify');

        $secondNotifierMock = $this->createMock(Notifier::class);
        $secondNotifierMock
            ->expects($this->exactly(2))
            ->method('supports')
            ->withConsecutive(
                [$firstCustomer, $notification],
                [$secondCustomer, $notification]
            )
            ->willReturnOnConsecutiveCalls(false, false);
        $secondNotifierMock
            ->expects($this->never())
            ->method('notify');

        $notifierFactoryMock = $this->createMock(NotifierFactoryInterface::class);
        $notifierFactoryMock->expects($this->once())
            ->method('createAllOrdered')
            ->with()
            ->willReturn([$firstNotifierMock, $secondNotifierMock]);

        $service = new NotificationService(
            eventBus: $eventBusMock,
            clock: $clockMock,
            notifierFactory: $notifierFactoryMock
        );
        $service->send(
            new Customers([$firstCustomer, $secondCustomer]),
            $notification
        );
    }

    public function testSendTwoCustomersFirstNotSuccessfulSecondSuccessful(): void
    {
        $currentDateTime = new DateTimeImmutable('2022-01-01');
        $clockMock = $this->createMock(Clock::class);
        $clockMock
            ->expects($this->exactly(2))
            ->method('getCurrentDateTime')
            ->with()
            ->willReturn($currentDateTime);

        $exampleLanguage = new Language('en');
        $firstCustomer = Customer::createNew(
            language: $exampleLanguage,
            email: new Email('test@test.com'),
            phoneNumber: new PhoneNumber('123123123'),
            deviceToken: new DeviceToken('a6345d0278adc55d3474f5')
        );
        $secondCustomer = Customer::createNew(
            language: $exampleLanguage,
            email: new Email('test2@test.com'),
            phoneNumber: new PhoneNumber('123123121'),
            deviceToken: new DeviceToken('a6345d0278adc55d3474fa')
        );
        $notification = Notification::createNew(
            [
                $exampleLanguage->toString() => new NotificationMessage(
                    language: $exampleLanguage,
                    title: 'example_title',
                    text: 'example_text'
                )
            ],
            $exampleLanguage
        );

        $eventBusMock = $this->createMock(EventBus::class);
        $eventBusMock
            ->expects($this->exactly(2))
            ->method('dispatch')
            ->withConsecutive(
                [new NotFoundChannelEvent(
                    userId: $firstCustomer->getId(),
                    notificationId: $notification->getId(),
                    eventDate: $currentDateTime
                )],
                [new SentNotificationEvent(
                    userId: $secondCustomer->getId(),
                    notificationId: $notification->getId(),
                    eventDate: $currentDateTime
                )]
            );

        $firstNotifierMock = $this->createMock(Notifier::class);
        $firstNotifierMock
            ->expects($this->exactly(2))
            ->method('supports')
            ->withConsecutive(
                [$firstCustomer, $notification],
                [$secondCustomer, $notification]
            )
            ->willReturnOnConsecutiveCalls(false, false);
        $firstNotifierMock
            ->expects($this->never())
            ->method('notify');

        $secondNotifierMock = $this->createMock(Notifier::class);
        $secondNotifierMock
            ->expects($this->exactly(2))
            ->method('supports')
            ->withConsecutive(
                [$firstCustomer, $notification],
                [$secondCustomer, $notification]
            )
            ->willReturnOnConsecutiveCalls(false, true);
        $secondNotifierMock
            ->expects($this->once())
            ->method('notify')
            ->with($secondCustomer, $notification)
            ->willReturn(true);

        $notifierFactoryMock = $this->createMock(NotifierFactoryInterface::class);
        $notifierFactoryMock->expects($this->once())
            ->method('createAllOrdered')
            ->with()
            ->willReturn([$firstNotifierMock, $secondNotifierMock]);

        $service = new NotificationService(
            eventBus: $eventBusMock,
            clock: $clockMock,
            notifierFactory: $notifierFactoryMock
        );
        $service->send(
            new Customers([$firstCustomer, $secondCustomer]),
            $notification
        );
    }
}