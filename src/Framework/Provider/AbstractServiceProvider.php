<?php

declare(strict_types=1);

namespace App\Framework\Provider;

use Override;
use App\Framework\Config\Configuration;
use App\Framework\DIContainer\ContainerInterface;

abstract class AbstractServiceProvider implements ServiceProviderInterface
{
    protected array $bindings = [];

    final public function __construct(protected Configuration $config, protected ContainerInterface $container)
    {
    }

    #[Override]
    final public function register(): void
    {
        $this->addMoreBindings();

        foreach ($this->bindings as $abstract => $concreteImpl) {
            $this->container->set($abstract, $concreteImpl);
        }
    }

    abstract protected function addMoreBindings(): void;
}
