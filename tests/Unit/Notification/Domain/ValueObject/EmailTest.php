<?php

namespace App\Tests\Notification\Domain\ValueObject;

use App\Notification\Domain\ValueObject\Email;
use Assert\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    public function testProperEmail(): void
    {
        $emailString = 'proper@email.com';
        $email = new Email($emailString);
        $this->assertEquals($emailString, $email->toString());
    }

    public function testInvalidEmail(): void
    {
        $emailString = 'invalid@email';
        $this->expectException(InvalidArgumentException::class);
        $email = new Email($emailString);
    }
}
