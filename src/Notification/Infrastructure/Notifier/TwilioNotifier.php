<?php

namespace App\Notification\Infrastructure\Notifier;

use App\Notification\Domain\Entity\Customer;
use App\Notification\Domain\Entity\Notification;
use App\Notification\Domain\Notifier\Notifier;
use App\Notification\Domain\ValueObject\PhoneNumber;
use LogicException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class TwilioNotifier implements Notifier
{
    private const NAME = 'twilio';

    private readonly Client $client;
    private readonly string $authorizedPhoneNumber;

    /** @param array{"sid": string, "key": string, "phone_number": string} $twilioConfig */
    public function __construct(array $twilioConfig)
    {
        $this->client = new Client($twilioConfig['sid'], $twilioConfig['key']);
        $this->authorizedPhoneNumber = $twilioConfig['phone_number'];
    }

    public static function getName(): string
    {
        return self::NAME;
    }

    public function supports(Customer $customer, Notification $notification): bool
    {
        return $customer->getPhoneNumber() instanceof PhoneNumber;
    }

    public function notify(Customer $customer, Notification $notification): bool
    {
        $notificationMessage = $notification->getMessage($customer->getLanguage());
        try {
            $this->client->messages->create(
                to: $customer->getPhoneNumber()?->toString() ?? throw new LogicException(),
                options: [
                    'from' => $this->authorizedPhoneNumber,
                    'body' => $notificationMessage->getText()
                ]
            );
        } catch (TwilioException) {
            return false;
        }
        return true;
    }
}
