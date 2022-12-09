<?php

namespace App\Tests\Notification\Infrastructure\Notifier;

use App\Notification\Domain\Entity\Customer;
use App\Notification\Domain\Entity\Notification;
use App\Notification\Domain\Entity\NotificationMessage;
use App\Notification\Domain\ValueObject\Email;
use App\Notification\Domain\ValueObject\Language;
use App\Notification\Domain\ValueObject\PhoneNumber;
use App\Notification\Infrastructure\Notifier\SesNotifier;
use App\Notification\Infrastructure\Notifier\TwilioNotifier;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TwilioNotifierTest extends KernelTestCase
{
    private array $twilioConfig;
    private string $twilioTestPhoneNumber;

    protected function setUp(): void
    {
        parent::setUp();
        $kernel = parent::bootKernel();

        $this->twilioConfig = [
            'sid' => $kernel->getContainer()->getParameter('twilio_sid'),
            'key' => $kernel->getContainer()->getParameter('twilio_key'),
            'phone_number' => $kernel->getContainer()->getParameter('twilio_phone_number')
        ];
        $this->twilioTestPhoneNumber = $kernel->getContainer()->getParameter('twilio_test_phone_number');
    }

    public function testSendEmail(): void
    {
        $sesNotifier = new TwilioNotifier($this->twilioConfig);
        $language = new Language('en');
        $result = $sesNotifier->notify(
            Customer::createNew(
                language: $language,
                email: null,
                phoneNumber: new PhoneNumber($this->twilioTestPhoneNumber),
                deviceToken: null
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