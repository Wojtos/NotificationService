<?php

namespace App\Notification\Domain\Entity;

use App\Notification\Domain\ValueObject\DeviceToken;
use App\Notification\Domain\ValueObject\Email;
use App\Notification\Domain\ValueObject\Language;
use App\Notification\Domain\ValueObject\PhoneNumber;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class Customer
{
    private function __construct(
        private readonly UuidInterface $id,
        private readonly Language $language,
        private readonly ?Email $email,
        private readonly ?PhoneNumber $phoneNumber,
        private readonly ?DeviceToken $deviceToken
    ) {
    }

    public static function createNew(
        Language $language,
        ?Email $email,
        ?PhoneNumber $phoneNumber,
        ?DeviceToken $deviceToken
    ): self {
        return new self(
            Uuid::uuid4(),
            $language,
            $email,
            $phoneNumber,
            $deviceToken
        );
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getLanguage(): Language
    {
        return $this->language;
    }

    public function getEmail(): ?Email
    {
        return $this->email;
    }

    public function getPhoneNumber(): ?PhoneNumber
    {
        return $this->phoneNumber;
    }

    public function getDeviceToken(): ?DeviceToken
    {
        return $this->deviceToken;
    }
}
