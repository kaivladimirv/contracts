<?php

declare(strict_types=1);

namespace App\Framework\Middleware;

use Override;
use App\Framework\DIContainer\ContainerInterface;
use App\Framework\Http\ServerRequestInterface;
use App\Framework\Http\ResponseInterface;
use App\Framework\Pipeline\Pipeline;
use App\Framework\Router\RouteNotFoundException;
use App\Framework\Router\Router;

readonly class FindRouteMiddleware implements MiddlewareInterface
{
    public function __construct(private Router $router, private ContainerInterface $container, private MiddlewareCollection $middlewareCollection)
    {
    }

    /**
     * @throws RouteNotFoundException
     */
    #[Override]
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route = $this->router->match($request);

        $request = $request->withAttributes($route->getAttributes())
            ->withAttribute('route', $route);

        if ($route->getMiddlewares()) {
            $pipeline = new Pipeline($this->container, $handler);
            foreach ($route->getMiddlewares() as $middlewareAlias) {
                if ($middleware = $this->middlewareCollection->find($middlewareAlias)) {
                    $pipeline->addPipe($middleware);
                }
            }

            return $pipeline->handle($request);
        }

        return $handler->handle($request);
    }
}
