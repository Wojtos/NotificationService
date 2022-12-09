<?php

namespace App\Notification\Infrastructure\Notifier;

use App\Notification\Domain\Notifier\Notifier;
use App\Notification\Domain\Notifier\NotifierFactoryInterface;
use App\Notification\Infrastructure\Notifier\Configuration\NotifierConfiguration;
use App\Notification\Infrastructure\Notifier\Configuration\NotifierFactoryConfiguration;
use App\Notification\Infrastructure\Notifier\Exception\NotAvailableNotifierException;

use function array_map;

class NotifierFactory implements NotifierFactoryInterface
{
    /**
     * @param array{"sid": string, "key": string, "phone_number": string} $twilioConfig
     * @param array{"verified_email": string} $awsConfig
     */

    public function __construct(
        private readonly NotifierFactoryConfiguration $notifierFactoryConfiguration,
        private readonly array $awsConfig,
        private readonly array $twilioConfig,
        private readonly string $pushyKey,
    ) {
    }

    /** @throws NotAvailableNotifierException*/
    public function create(string $name): Notifier
    {
        return match ($name) {
            SesNotifier::getName() => new SesNotifier($this->awsConfig),
            TwilioNotifier::getName() => new TwilioNotifier($this->twilioConfig),
            PushyNotifier::getName() => new PushyNotifier($this->pushyKey),
            default => throw new NotAvailableNotifierException($name)
        };
    }

    /**
     * @return array<Notifier>
     * @throws NotAvailableNotifierException
     */
    public function createAllOrdered(): array
    {
        return array_map(
            fn(NotifierConfiguration $notifierConfiguration): Notifier =>
                $this->create($notifierConfiguration->getNotifierName()),
            $this->notifierFactoryConfiguration->getOrderedActiveNotifierConfigurations()
        );
    }
}
