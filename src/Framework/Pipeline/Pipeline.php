<?php

declare(strict_types=1);

namespace App\Framework\Pipeline;

use Override;
use App\Framework\DIContainer\ContainerInterface;
use App\Framework\Http\ServerRequestInterface;
use App\Framework\Http\ResponseInterface;
use App\Framework\Middleware\MiddlewareInterface;
use App\Framework\Middleware\RequestHandlerInterface;
use InvalidArgumentException;

class Pipeline implements PipelineInterface, RequestHandlerInterface
{
    private array $middlewares = [];

    public function __construct(private readonly ContainerInterface $container, private readonly ?RequestHandlerInterface $fallbackHandler = null)
    {
    }

    #[Override]
    public function addPipe(MiddlewareInterface|string $middleware): self
    {
        $this->throwExceptionIfNotImplementsMiddlewareInterface($middleware);

        $this->middlewares[] = $middleware;

        return $this;
    }

    private function throwExceptionIfNotImplementsMiddlewareInterface($middleware): void
    {
        if (!in_array(MiddlewareInterface::class, class_implements($middleware))) {
            throw new InvalidArgumentException('Middleware должен реализовывать MiddlewareInterface');
        }
    }

    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->next($request);
    }

    private function next(ServerRequestInterface $request): ResponseInterface
    {
        if (!$middleware = array_shift($this->middlewares)) {
            return $this->fallbackHandler->handle($request);
        }

        return $this->resolveMiddleware($middleware)->process($request, $this);
    }

    private function resolveMiddleware($middleware): MiddlewareInterface
    {
        if (is_string($middleware)) {
            return $this->container->get($middleware);
        }

        return $middleware;
    }
}
