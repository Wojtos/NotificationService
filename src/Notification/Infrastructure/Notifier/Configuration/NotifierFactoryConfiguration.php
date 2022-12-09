<?php

namespace App\Notification\Infrastructure\Notifier\Configuration;

use function array_filter;

final class NotifierFactoryConfiguration
{
    /** @param array<NotifierConfiguration> $orderedNotifierConfigurations */
    public function __construct(
        private readonly array $orderedNotifierConfigurations
    ) {
    }

    /** @return array<NotifierConfiguration> */
    public function getOrderedActiveNotifierConfigurations(): array
    {
        return array_filter(
            $this->orderedNotifierConfigurations,
            fn(NotifierConfiguration $configuration): bool => $configuration->isEnabled()
        );
    }
}
