<?php

namespace App\Tests\Notification\Infrastructure\Notifier;

use App\Notification\Domain\Entity\Customer;
use App\Notification\Domain\Entity\Notification;
use App\Notification\Domain\Entity\NotificationMessage;
use App\Notification\Domain\ValueObject\Email;
use App\Notification\Domain\ValueObject\Language;
use App\Notification\Infrastructure\Notifier\SesNotifier;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SesNotifierTest extends KernelTestCase
{
    private array $awsConfig;
    private string $awsTestEmail;

    protected function setUp(): void
    {
        parent::setUp();
        $kernel = parent::bootKernel();

        $this->awsConfig = [
            'region' => $kernel->getContainer()->getParameter('aws_region'),
            'version' => $kernel->getContainer()->getParameter('aws_version'),
            'verified_email' => $kernel->getContainer()->getParameter('aws_verified_email'),
            'credentials' => [
                'key' => $kernel->getContainer()->getParameter('aws_key'),
                'secret' => $kernel->getContainer()->getParameter('aws_secret')
        ]];
        $this->awsTestEmail = $kernel->getContainer()->getParameter('aws_test_email');
    }

    public function testSendEmail(): void
    {
        $sesNotifier = new SesNotifier($this->awsConfig);
        $language = new Language('en');
        $result = $sesNotifier->notify(
            Customer::createNew(
                language: $language,
                email: new Email($this->awsTestEmail),
                phoneNumber: null,
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