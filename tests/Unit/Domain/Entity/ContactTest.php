<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Entity;

use App\Domain\Entity\Contact;
use App\Domain\Exception\DuplicatePhoneException;
use App\Domain\Exception\InvalidEmailException;
use PHPUnit\Framework\TestCase;

final class ContactTest extends TestCase
{
    public function testCreateWithValidDataReturnsInstance(): void
    {
        $contact = Contact::create(
            'Maria',
            'Perez',
            'maria@example.com',
            ['+573001234567', '+573007654321'],
        );

        $this->assertNotSame('', $contact->id()->value());
        $this->assertSame('Maria', $contact->name()->value());
        $this->assertSame('maria@example.com', $contact->email()->value());
        $this->assertCount(2, $contact->phones());
        $this->assertSame(
            $contact->createdAt()->format('Y-m-d H:i:s'),
            $contact->updatedAt()->format('Y-m-d H:i:s'),
        );
    }

    public function testUpdateNameChangesUpdatedAt(): void
    {
        $contact = Contact::create('Maria', 'Perez', 'maria@example.com', []);
        $originalUpdatedAt = $contact->updatedAt();

        usleep(1000);
        $contact->updateName('Mariana');

        $this->assertGreaterThan($originalUpdatedAt, $contact->updatedAt());
    }

    public function testCreateWithDuplicatePhonesThrowsException(): void
    {
        $this->expectException(DuplicatePhoneException::class);

        (void) Contact::create('Test', 'User', 'test@test.com', [
            '+573001111111',
            '+573001111111',
        ]);
    }

    public function testCreateWithInvalidEmailThrowsException(): void
    {
        $this->expectException(InvalidEmailException::class);

        (void) Contact::create('Test', 'User', 'bad-email', []);
    }
}
