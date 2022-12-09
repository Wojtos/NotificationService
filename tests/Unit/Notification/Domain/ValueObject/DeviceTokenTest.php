<?php

namespace App\Tests\Notification\Domain\ValueObject;

use App\Notification\Domain\ValueObject\DeviceToken;
use Assert\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DeviceTokenTest extends TestCase
{
    public function testProperToken(): void
    {
        $tokenString = 'a6345d0278adc55d3474f5';
        $deviceToken = new DeviceToken($tokenString);
        $this->assertEquals($tokenString, $deviceToken->toString());
    }

    public function testTooLongToken(): void
    {
        $tokenString = 'a6345d0278adc55d3474f5a';
        $this->expectException(InvalidArgumentException::class);
        $deviceToken = new DeviceToken($tokenString);
    }

    public function testTooShortToken(): void
    {
        $tokenString = 'a6345d0278adc55d3474f';
        $this->expectException(InvalidArgumentException::class);
        $deviceToken = new DeviceToken($tokenString);
    }
}
