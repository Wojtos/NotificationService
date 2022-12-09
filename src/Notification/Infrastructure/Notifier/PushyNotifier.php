<?php

namespace App\Notification\Infrastructure\Notifier;

use App\Notification\Domain\Entity\Customer;
use App\Notification\Domain\Entity\Notification;
use App\Notification\Domain\Notifier\Notifier;
use App\Notification\Domain\ValueObject\DeviceToken;
use Exception;
use LogicException;

use function curl_close;
use function curl_exec;
use function curl_init;
use function curl_setopt;
use function json_decode;

class PushyNotifier implements Notifier
{
    private const NAME = 'pushy';

    public function __construct(
        private readonly string $pushyKey
    ) {
    }

    public static function getName(): string
    {
        return self::NAME;
    }

    public function supports(Customer $customer, Notification $notification): bool
    {
        return $customer->getDeviceToken() instanceof DeviceToken;
    }

    public function notify(Customer $customer, Notification $notification): bool
    {
        $notificationMessage = $notification->getMessage($customer->getLanguage());
        try {
            $postData = [
                'to' => $customer->getDeviceToken()?->toString() ?? throw new LogicException(),
                'data' => [
                    'message' => $notificationMessage->getText()
                ],
                'notification' => [
                    'title' => $notificationMessage->getTitle(),
                    'body'  => $notificationMessage->getText()
                ]
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.pushy.me/push?api_key=' . $this->pushyKey);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, (string) json_encode($postData, JSON_UNESCAPED_UNICODE));
            $result = (string) curl_exec($ch);
            curl_close($ch);
            $response = json_decode($result);
            if (isset($response) && isset($response->error)) {
                return false;
            }
        } catch (Exception) {
            return false;
        }
        return true;
    }
}
