<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

readonly class Request
{
    /**
     * @param array<string, mixed> $queryParams
     * @param array<string, mixed> $body
     * @param array<string, string> $headers
     * @param array<string, string> $routeParams
     */

    private function __construct(
        private string $method,
        private string $uri,
        private string $path,
        private array $queryParams,
        private array $body,
        private array $headers,
        private array $routeParams = [],
    ) {}


    public static function fromGlobals(): self
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri    = $_SERVER['REQUEST_URI'] ?? '/';
        $path   = rawurldecode(parse_url($uri, PHP_URL_PATH) ?? '/');
 
        return new self(
            method: strtoupper($method),
            uri: $uri,
            path: $path,
            queryParams: $_GET,
            body: self::parseBody(),
            headers: self::extractHeaders($_SERVER),
        );
    }

    private static function parseBody(): array
    {
        $raw = file_get_contents('php://input');
 
        if ($raw === false || $raw === '') {
            return [];
        }
 
        $decoded = json_decode($raw, true);
 
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [];
        }
 
        return is_array($decoded) ? $decoded : [];
    }

    private static function extractHeaders(array $server): array
    {
        $headers = [];
 
        foreach ($server as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $name = str_replace('_', '-', substr($key, 5));
                $headers[strtolower($name)] = $value;
 
                continue;
            }
 
            if ($key === 'CONTENT_TYPE' || $key === 'CONTENT_LENGTH') {
                $name = str_replace('_', '-', $key);
                $headers[strtolower($name)] = $value;
            }
        }
 
        return $headers;
    }

    public static function create(
        string $method,
        string $uri,
        array $queryParams = [],
        array $body = [],
        array $headers = [],
        array $routeParams = [],
    ): self {
        return new self(
            method: strtoupper($method),
            uri: $uri,
            path: parse_url($uri, PHP_URL_PATH) ?? '/',
            queryParams: $queryParams,
            body: $body,
            headers: array_change_key_case($headers, CASE_LOWER),
            routeParams: $routeParams,
        );
    }

    public function withRouteParam(string $key, string $value): self
    {
        return new self(
            method: $this->method,
            uri: $this->uri,
            path: $this->path,
            queryParams: $this->queryParams,
            body: $this->body,
            headers: $this->headers,
            routeParams: array_merge($this->routeParams, [$key => $value]),
        );
    }


    public function method(): string
    {
        return $this->method;
    }

    public function path(): string
    {
        return $this->path;
    }
 
    public function uri(): string
    {
        return $this->uri;
    }
 
    public function queryParams(): array
    {
        return $this->queryParams;
    }
 
    public function query(string $key, mixed $default = null): mixed
    {
        return $this->queryParams[$key] ?? $default;
    }
 
    public function body(): array
    {
        return $this->body;
    }
 
    public function input(string $key, mixed $default = null): mixed
    {
        return $this->body[$key] ?? $default;
    }
 
    public function routeParams(): array
    {
        return $this->routeParams;
    }
 
    public function routeParam(string $key): ?string
    {
        return $this->routeParams[$key] ?? null;
    }
 
    public function header(string $name): ?string
    {
        return $this->headers[strtolower($name)] ?? null;
    }

}