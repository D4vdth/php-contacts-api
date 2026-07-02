<?php
declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Exception\DuplicatePhoneException;
use App\Domain\Exception\InvalidPhoneException;
use App\Domain\ValueObject\Email;
use App\Domain\ValueObject\LastName;
use App\Domain\ValueObject\Name;
use App\Domain\ValueObject\Phone;
use App\Domain\ValueObject\Uuid;
use DateTimeImmutable;

class Contact
{
    /** @var Phone[] */
    private array $phones;

    private function __construct(
        private readonly Uuid $id, 
        private Name $name, 
        private LastName $lastName, 
        private Email $email, 
        private DateTimeImmutable $createdAt, 
        private DateTimeImmutable $updatedAt, 
        array $phones = [])
    {
        $this->phones = $phones;
    }


    /**
     * @param Phone[] $phones 
     */

    #[\NoDiscard]
    public static function create(
        string $name, 
        string $lastName, 
        string $email, 
        array $phones = []
    ): self {
        self::validateNoDuplicatePhones($phones);

        return new self(
            id: Uuid::generate(),
            name: Name::create($name),
            lastName: LastName::create($lastName),
            email: Email::create($email),
            createdAt:new DateTimeImmutable(),
            updatedAt: new DateTimeImmutable(),
            phones:$phones
        );

    }

    #[\NoDiscard]
    public static function reconstitute(
        string $id,                    
        string $name,                  
        string $lastName,              
        string $email,                 
        array $phones,                 
        DateTimeImmutable $createdAt, 
        DateTimeImmutable $updatedAt
    ): self {
        
        return new self(
            id: Uuid::fromString($id),
            name: Name::create($name),
            lastName: LastName::create($lastName),
            email: Email::create($email),
            createdAt: $createdAt,
            updatedAt: $updatedAt,
            phones:$phones
        );
    }

    /**
     * @param Phone[] $phones 
     */

    public function replacePhones(array $phones): void
    {
        self::validateNoDuplicatePhones($phones);
        $this->updatedAt = new DateTimeImmutable();

        $this->phones = $phones;
    }

    /**
     * @param Phone[] $phones 
     */

    private static function validateNoDuplicatePhones(array $phones): void
    {
        $seen = [];

        foreach ($phones as $phone) {
            if (!$phone instanceof Phone){
                throw InvalidPhoneException::withValue($phone);
            };

            $number = $phone->value();

            if (isset($seen[$number])) {
                throw new DuplicatePhoneException($number);
            }

            $seen[$number] = true;
        }
    }


    public function id(): Uuid
    {
        return $this->id;
    }
    
    public function name(): Name
    {
        return $this->name;
    }

     public function lastName(): LastName
    {
        return $this->lastName;
    }

    public function email(): Email
    {
        return $this->email;
    }

    /**
     * @return Phone[]
     */
    public function phones(): array
    {
        return $this->phones;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }


    public function toArray(): array
    {
        return [
            'id' => $this->id->value(),
            'name' => $this->name->value(),
            'lastName' => $this->lastName->value(),
            'email' => $this->email->value(),
            'phones' => array_map(
                fn (Phone $phone): string => $phone->value(),
                $this->phones,
            ),
            'createdAt' => $this->createdAt->format(DateTimeImmutable::ATOM),
            'updatedAt' => $this->updatedAt->format(DateTimeImmutable::ATOM),
        ];
    }


    public function updateName(string $name): void
    {
        $this->name = Name::create($name);
        $this->updatedAt = new DateTimeImmutable();
    }

    public function updateLastName(string $lastName): void
    {
        $this->lastName = LastName::create($lastName);
        $this->updatedAt = new DateTimeImmutable();
    }

    public function updateEmail(string $email): void
    {
        $this->email = Email::create($email);
        $this->updatedAt = new DateTimeImmutable();
    }


}
