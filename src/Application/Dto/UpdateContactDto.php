<?php 

declare(strict_types=1);

namespace App\Application\Dto;


readonly class UpdateContactDto
{
    public function __construct(
        public string $id,
        public string $name,
        public string $lastName,
        public string $email,
        public array $phones
    ){}

    public static function fromRequest(string $id, array $body): self
    {
        return new self(
            id: $id,
            name: $body['name'] ?? '',
            lastName: $body['last_name'] ?? '',
            email: $body['email'] ?? '',
            phones: $body['phones'] ?? [],
        );
    }
}