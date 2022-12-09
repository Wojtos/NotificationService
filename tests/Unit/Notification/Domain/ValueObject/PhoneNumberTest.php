<?php

namespace App\Tests\Notification\Domain\ValueObject;

use App\Notification\Domain\ValueObject\PhoneNumber;
use Assert\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PhoneNumberTest extends TestCase
{
    public function testProperNumberWithPlus(): void
    {
        $phoneNumberString = '+48123456789';
        $phoneNumber = new PhoneNumber($phoneNumberString);
        $this->assertEquals($phoneNumberString, $phoneNumber->toString());
    }

    public function testProperSimpleNumber(): void
    {
        $phoneNumberString = '123456789';
        $phoneNumber = new PhoneNumber($phoneNumberString);
        $this->assertEquals($phoneNumberString, $phoneNumber->toString());
    }

    public function testWrongNumber(): void
    {
        $phoneNumberString = 'a123456789';
        $this->expectException(InvalidArgumentException::class);
        $phoneNumber = new PhoneNumber($phoneNumberString);
    }

}
