<?php

namespace App\Tests\Notification\Domain\ValueObject;

use App\Notification\Domain\ValueObject\Language;
use Assert\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class LanguageTest extends TestCase
{
    public function testProperEmail(): void
    {
        $languageString = 'en';
        $language = new Language($languageString);
        $this->assertEquals($languageString, $language->toString());
    }

    public function testInvalidEmail(): void
    {
        $languageString = 'lt';
        $this->expectException(InvalidArgumentException::class);
        $language = new Language($languageString);
    }
}
