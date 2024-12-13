<?php

declare(strict_types=1);

namespace App\Framework\Middleware;

use App\Framework\Config\Configuration;

readonly class MiddlewaresLoader
{
    public function __construct(private Configuration $middlewareConfig)
    {
    }

    public function load(): MiddlewareCollection
    {
        $middlewareCollection = new MiddlewareCollection();
        foreach ($this->middlewareConfig->getParam('middlewares') as $alias => $className) {
            $middlewareCollection->add($alias, $className);
        }

        return $middlewareCollection;
    }
}
