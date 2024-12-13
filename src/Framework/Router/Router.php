<?php

declare(strict_types=1);

namespace App\Framework\Router;

use App\Framework\Http\ServerRequestInterface;

readonly class Router
{
    public function __construct(private RouteCollection $routes)
    {
    }

    /**
     * @throws RouteNotFoundException
     */
    public function match(ServerRequestInterface $request): Route
    {
        foreach ($this->routes->getRoutes() as $route) {
            if ($route->match($request)) {
                return $route;
            }
        }

        throw new RouteNotFoundException("Маршрут {$request->getUriPath()} не найден");
    }
}
