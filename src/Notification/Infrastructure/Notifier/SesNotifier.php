<?php

namespace App\Notification\Infrastructure\Notifier;

use App\Notification\Domain\Entity\Customer;
use App\Notification\Domain\Entity\Notification;
use App\Notification\Domain\Notifier\Notifier;
use App\Notification\Domain\ValueObject\Email;
use Aws\Exception\AwsException;
use Aws\Ses\SesClient;
use LogicException;

class SesNotifier implements Notifier
{
    private const NAME = 'ses';

    private readonly SesClient $sesClient;
    private readonly string $verifiedEmail;

    /** @param array{"verified_email": string} $awsConfig */
    public function __construct(
        array $awsConfig
    ) {
        $this->verifiedEmail = $awsConfig['verified_email'];
        $this->sesClient = new SesClient($awsConfig);
    }

    public static function getName(): string
    {
        return self::NAME;
    }

    public function supports(Customer $customer, Notification $notification): bool
    {
        return $customer->getEmail() instanceof Email;
    }

    public function notify(Customer $customer, Notification $notification): bool
    {
        $notificationMessage = $notification->getMessage($customer->getLanguage());
        try {
            $this->sesClient->sendEmail([
                'Destination' => [
                    'ToAddresses' => [$customer->getEmail()?->toString() ?? throw new LogicException()],
                ],
                'Message' => [
                    'Body' => [
                        'Text' => [
                            'Data' => $notificationMessage->getText(),
                        ],
                    ],
                    'Subject' => [
                        'Data' => $notificationMessage->getTitle(),
                    ],
                ],
                'Source' => $this->verifiedEmail,
            ]);
        } catch (AwsException) {
            return false;
        }
        return true;
    }
}
