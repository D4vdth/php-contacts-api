<?php 

declare(strict_types=1);

namespace App\Application\Dto;


readonly class UpdateContactDto
{
    public function __construct(
        public string $name,
        public string $lastName,
        public string $email,
        public array $phones
    ){}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? '',
            lastName: $data['last_name'] ?? '',
            email: $data['email'] ?? '',
            phones: $data['phones'] ?? [],
        );
    }
}