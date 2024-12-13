<?php

declare(strict_types=1);

namespace App\Framework\Router;

use App\Framework\Config\Configuration;
use UnexpectedValueException;

readonly class RoutesLoader
{
    public function __construct(private Configuration $routesConfig)
    {
    }

    public function load(): RouteCollection
    {
        $routeCollection = new RouteCollection();

        foreach ($this->routesConfig->getParams() as $routeMethodAndUri => $routeData) {
            $routeHttpMethod = $this->extractHttpMethodFrom($routeMethodAndUri);
            $routeUri = $this->extractUriFrom($routeMethodAndUri);

            $this->throwExceptionIfHandlerIsNotSpecified($routeData);

            $routeCollection->addRoute(
                $this->buildRoute($routeUri, $routeHttpMethod, $routeData)
            );
        }

        return $routeCollection;
    }

    private function extractHttpMethodFrom(string $data): string
    {
        return explode(':', $data)[0];
    }

    private function extractUriFrom(string $data): string
    {
        return explode(':', $data)[1];
    }

    private function throwExceptionIfHandlerIsNotSpecified(array $routeData): void
    {
        if (empty($routeData['handler'])) {
            throw new UnexpectedValueException('Не указан обработчик маршрута');
        }
    }

    private function buildRoute(string $routeUri, string $routeHttpMethod, array $routeData): Route
    {
        $route = new Route($routeUri, explode('::', (string) $routeData['handler']), [$routeHttpMethod]);

        foreach ($this->extractMiddlewaresFrom($routeData) as $middlewareAlias) {
            $route->addMiddleware(trim((string) $middlewareAlias));
        }

        foreach ($this->extractExcludedMiddlewaresFrom($routeData) as $middlewareAlias) {
            $route->excludeMiddleware(trim((string) $middlewareAlias));
        }

        return $route;
    }

    private function extractMiddlewaresFrom(array $routeData): array
    {
        return (!empty($routeData['middlewares']) ? explode(',', (string) $routeData['middlewares']) : []);
    }

    private function extractExcludedMiddlewaresFrom(array $routeData): array
    {
        return (!empty($routeData['withoutMiddlewares']) ? explode(',', (string) $routeData['withoutMiddlewares']) : []);
    }
}
