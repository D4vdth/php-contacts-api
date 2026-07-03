<?php

declare(strict_types=1);

namespace App\Infrastructure\Config;

final class Container
{
    /** @var array<string, callable> */
    private array $factories = [];

    /** @var array<string, mixed> */
    private array $instances = [];

    public function set(string $id, callable $factory): void
    {
        $this->factories[$id] = $factory;
    }

    public function get(string $id): mixed
    {
        if (array_key_exists($id, $this->instances)) {
            return $this->instances[$id];
        }

        if (array_key_exists($id, $this->factories)) {
            $instance = ($this->factories[$id])($this);
            $this->instances[$id] = $instance;

            return $instance;
        }

        throw new \RuntimeException("Service not found: {$id}");
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->factories) || array_key_exists($id, $this->instances);
    }
}
