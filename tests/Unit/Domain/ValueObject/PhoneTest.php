<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\ValueObject;

use App\Domain\Exception\InvalidPhoneException;
use App\Domain\ValueObject\Phone;
use PHPUnit\Framework\TestCase;

final class PhoneTest extends TestCase
{
    public function testCreateWithValidE164PhoneReturnsInstance(): void
    {
        $phone = Phone::create('+573001234567');

        $this->assertSame('+573001234567', $phone->value());
    }

    public function testCreateWithoutPlusSignThrowsException(): void
    {
        $this->expectException(InvalidPhoneException::class);

        (void) Phone::create('573001234567');
    }

    public function testCreateWithLettersThrowsException(): void
    {
        $this->expectException(InvalidPhoneException::class);

        (void) Phone::create('+57abc');
    }
}
