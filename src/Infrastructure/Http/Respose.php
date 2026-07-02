<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

readonly class Response
{
    /**
     * @param array<string, string> $headers
     */

    private function __construct(
        private int $statusCode,
        private mixed $body,
        private array $headers = ['Content-Type' => 'application/json'],
    ) {}

    public static function json(mixed $data, int $status = 200): self
    {
        return new self(
            statusCode: $status,
            body: $data,
        );
    }

    public static function created(mixed $data): self
    {
        return new self(
            statusCode: 201,
            body: $data,
        );
    }

    public static function noContent(): self
    {
        return new self(
            statusCode: 204,
            body: null,
            headers: [],
        );
    }

    public static function badRequest(string|array $message): self
    {
        return new self(
            statusCode: 400,
            body: ['error' => $message],
        );
    }

    public static function notFound(string $message): self
    {
        return new self(
            statusCode: 404,
            body: ['error' => $message],
        );
    }

    public static function conflict(string $message): self
    {
        return new self(
            statusCode: 409,
            body: ['error' => $message],
        );
    }

    public static function unprocessableEntity(array $errors): self
    {
        return new self(
            statusCode: 422,
            body: ['errors' => $errors],
        );
    }

    public static function internalError(string $message = 'Internal server error'): self
    {
        return new self(
            statusCode: 500,
            body: ['error' => $message],
        );
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }

    public function send(): void
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }

        if ($this->body !== null) {
            echo json_encode($this->body, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        }
    }
}