<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\ValueObject;

use App\Domain\Exception\InvalidEmailException;
use App\Domain\ValueObject\Email;
use PHPUnit\Framework\TestCase;

final class EmailTest extends TestCase
{
    public function testCreateWithValidEmailReturnsInstance(): void
    {
        $email = Email::create('maria@example.com');

        $this->assertSame('maria@example.com', $email->value());
    }

    public function testCreateNormalizesEmailToLowercase(): void
    {
        $email = Email::create('MARIA@EXAMPLE.COM');

        $this->assertSame('maria@example.com', $email->value());
    }

    public function testCreateWithInvalidEmailThrowsException(): void
    {
        $this->expectException(InvalidEmailException::class);

        (void) Email::create('not-an-email');
    }

    public function testCreateWithEmptyStringThrowsException(): void
    {
        $this->expectException(InvalidEmailException::class);

        (void) Email::create('');
    }

    public function testIsEqualComparesEmailsByValue(): void
    {
        $email = Email::create('maria@example.com');
        $sameEmail = Email::create('maria@example.com');
        $differentEmail = Email::create('other@example.com');

        $this->assertTrue($email->isEqual($sameEmail));
        $this->assertFalse($email->isEqual($differentEmail));
    }
}
