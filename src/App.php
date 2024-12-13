<?php

declare(strict_types=1);

namespace App;

use App\Framework\Config\Configuration;
use App\Framework\Command\CommandInterface;
use App\Framework\Console\Argument\ArgumentTypes;
use App\Framework\Console\ConsoleInterface;
use App\Framework\Console\ExpectedArgument\ExpectedArgument;
use App\Framework\DIContainer\Container;
use App\Framework\DIContainer\ContainerInterface;
use App\Framework\Http\ServerRequestInterface;
use App\Framework\Http\Response;
use App\Framework\Middleware\FindRouteMiddleware;
use App\Framework\Middleware\HandleRouteMiddleware;
use App\Framework\Middleware\MiddlewareCollection;
use App\Framework\Middleware\MiddlewaresLoader;
use App\Framework\Pipeline\Pipeline;
use App\Framework\Router\Router;
use App\Framework\Router\RoutesLoader;
use Dotenv\Dotenv;
use Exception;
use ReflectionException;
use UnexpectedValueException;

class App
{
    private const string PATH_TO_CONFIG_INI_FILE      = '/Config/config.ini';
    private const string PATH_TO_ROUTES_INI_FILE      = '/Config/routes.ini';
    private const string PATH_TO_MIDDLEWARES_INI_FILE = '/Config/middlewares.ini';

    private ContainerInterface $container;
    private Configuration $config;
    private ConsoleInterface $console;

    /**
     * @throws ReflectionException
     */
    public function __construct()
    {
        $this->initialize();
    }

    /**
     * @throws ReflectionException
     */
    private function initialize(): void
    {
        $dotenv = Dotenv::createUnsafeImmutable(dirname(__DIR__));
        $dotenv->safeLoad();

        $this->container = new Container();
        $this->config = $this->getConfig();

        $this->registerProviders($this->config->getParam('providers'));

        $this->console = $this->container->get(ConsoleInterface::class);
    }

    private function getConfig(): Configuration
    {
        if (!$this->container->has(Configuration::class)) {
            $config = new Configuration(__DIR__ . self::PATH_TO_CONFIG_INI_FILE);
            $this->container->set(Configuration::class, $config);
        }

        return $this->container->get(Configuration::class);
    }

    private function registerProviders(array $providers): void
    {
        foreach ($providers as $providerClassName) {
            $provider = $this->container->get($providerClassName);
            $provider->register();
        }
    }

    public function run(): void
    {
        if ($this->isCliRequest()) {
            $this->handleConsoleRequest();
        } else {
            $this->handleHttpRequest();
        }
    }

    private function handleConsoleRequest(): void
    {
        try {
            $this->console->addExpectedArgument(new ExpectedArgument('commandName', ArgumentTypes::STRING));
            $commandName = $this->console->getArgumentByName('commandName')->getValue();

            $this->container->callMethod($this->getCommand($commandName), 'run');
        } catch (Exception $e) {
            $this->console->error('Error: ' . $e->getMessage());
        }
    }

    private function handleHttpRequest(): void
    {
        try {
            $this->initMiddlewaresCollection();
            $this->initRouter();

            $response = (new Pipeline($this->container))
                ->addPipe(FindRouteMiddleware::class)
                ->addPipe(HandleRouteMiddleware::class)
                ->handle($this->getRequest());

            $response->send();
        } catch (Exception $e) {
            $response = new Response(400, $e->getMessage());

            $response->send();
        }
    }

    private function initRouter(): void
    {
        $routesConfig = new Configuration(__DIR__ . self::PATH_TO_ROUTES_INI_FILE);
        $routeCollection = (new RoutesLoader($routesConfig))->load();

        $this->container->set(Router::class, new Router($routeCollection));
    }

    private function initMiddlewaresCollection(): void
    {
        $middlewaresConfig = new Configuration(__DIR__ . self::PATH_TO_MIDDLEWARES_INI_FILE);
        $middlewareCollection = (new MiddlewaresLoader($middlewaresConfig))->load();

        $this->container->set(MiddlewareCollection::class, $middlewareCollection);
    }

    private function getCommand(string $commandName): CommandInterface
    {
        $commands = $this->config->getParam('commands');

        if (empty($commands[$commandName])) {
            throw new UnexpectedValueException("Неизвестная команда $commandName");
        }

        return $this->container->get($commands[$commandName]);
    }

    private function isCliRequest(): bool
    {
        return (php_sapi_name() === 'cli');
    }

    private function getRequest(): ServerRequestInterface
    {
        return $this->container->get(ServerRequestInterface::class);
    }
}
