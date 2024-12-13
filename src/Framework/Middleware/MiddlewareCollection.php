<?php

declare(strict_types=1);

namespace App\Framework\Middleware;

class MiddlewareCollection
{
    private array $middlewares = [];

    public function find(string $alias): string
    {
        return (!empty($this->middlewares[$alias]) ? $this->middlewares[$alias] : '');
    }

    public function add(string $alias, string $className): void
    {
        $this->middlewares[$alias] = $className;
    }
}
