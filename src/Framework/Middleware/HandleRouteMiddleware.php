<?php

declare(strict_types=1);

namespace App\Framework\Middleware;

use Override;
use App\Framework\DIContainer\ContainerInterface;
use App\Framework\Http\ServerRequestInterface;
use App\Framework\Http\ResponseInterface;
use App\Framework\Router\Route;
use App\Framework\Router\RouteNotFoundException;

readonly class HandleRouteMiddleware implements MiddlewareInterface
{
    public function __construct(private ContainerInterface $container)
    {
    }

    /**
     * @throws RouteNotFoundException
     */
    #[Override]
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /* @var Route $route */
        if (!$route = $request->getAttribute('route')) {
            throw new RouteNotFoundException("Маршрут {$request->getUriPath()} не найден");
        }

        $this->container->set(ServerRequestInterface::class, $request);

        $handlerParams = $request->getAttributes();
        $handler = $this->container->get($route->getHandler()[0]);

        return $this->container->callMethod($handler, $route->getHandler()[1], $handlerParams);
    }
}
