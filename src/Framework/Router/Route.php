<?php

declare(strict_types=1);

namespace App\Framework\Router;

use App\Framework\Http\ServerRequestInterface;
use UnexpectedValueException;
use Weew\UrlMatcher\UrlMatcher;

class Route
{
    private readonly array $handler;
    private array $attributes          = [];
    private array $middlewares         = [];
    private array $excludedMiddlewares = [];

    public function __construct(private readonly string $uri, array $handler, private readonly array $methods)
    {
        $this->throwExceptionIfClassNotFound($handler[0]);
        $this->throwExceptionIfMethodNotFoundIn($handler[1], $handler[0]);
        $this->handler = $handler;
    }

    public function addMiddleware(string $middlewareAlias): void
    {
        $this->middlewares[] = trim($middlewareAlias);
    }

    public function excludeMiddleware(string $middlewareAlias): void
    {
        $this->excludedMiddlewares[] = trim($middlewareAlias);
    }

    private function throwExceptionIfClassNotFound(string $className): void
    {
        if (!class_exists($className)) {
            throw new UnexpectedValueException("Класс $className не найден");
        }
    }

    private function throwExceptionIfMethodNotFoundIn(string $methodName, string $className): void
    {
        if (!method_exists($className, $methodName)) {
            throw new UnexpectedValueException("Метод $methodName не найден в классе $className");
        }
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getMethods(): array
    {
        return $this->methods;
    }

    public function getHandler(): array
    {
        return $this->handler;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getExcludedMiddlewares(): array
    {
        return $this->excludedMiddlewares;
    }

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    public function match(ServerRequestInterface $request): bool
    {
        $this->attributes = [];

        $urlMatcher = new UrlMatcher();

        if (
            !in_array($request->getMethod(), $this->getMethods())
            or !$urlMatcher->match($this->getUri(), $request->getUriPath())
        ) {
            return false;
        }

        $this->attributes = $urlMatcher->parse($this->getUri(), $request->getUriPath())->toArray();

        return true;
    }
}
