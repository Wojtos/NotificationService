<?php

namespace App\Tests\Notification\Infrastructure\Notifier;

use App\Notification\Domain\Entity\Customer;
use App\Notification\Domain\Entity\Notification;
use App\Notification\Domain\Entity\NotificationMessage;
use App\Notification\Domain\ValueObject\DeviceToken;
use App\Notification\Domain\ValueObject\Email;
use App\Notification\Domain\ValueObject\Language;
use App\Notification\Domain\ValueObject\PhoneNumber;
use App\Notification\Infrastructure\Notifier\PushyNotifier;
use App\Notification\Infrastructure\Notifier\SesNotifier;
use App\Notification\Infrastructure\Notifier\TwilioNotifier;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PushyNotifierTest extends KernelTestCase
{
    private string $pushyTestDeviceToken;
    private string $pushyKey;

    protected function setUp(): void
    {
        parent::setUp();
        $kernel = parent::bootKernel();

        $this->pushyKey = $kernel->getContainer()->getParameter('pushy_key');
        $this->pushyTestDeviceToken = $kernel->getContainer()->getParameter('pushy_test_device_token');
    }

    public function testSendEmail(): void
    {
        $sesNotifier = new PushyNotifier($this->pushyKey);
        $language = new Language('en');
        $result = $sesNotifier->notify(
            Customer::createNew(
                language: $language,
                email: null,
                phoneNumber: null,
                deviceToken: new DeviceToken($this->pushyTestDeviceToken)
            ),
            Notification::createNew(
                messagesByLanguage: [
                    $language->toString() => new NotificationMessage(
                        language: $language,
                        title: 'example_title',
                        text: 'example_text'
                    )
                ],
                fallBackLanguage: $language
            )
        );
        $this->assertTrue($result);
    }
}