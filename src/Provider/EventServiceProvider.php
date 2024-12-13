<?php

declare(strict_types=1);

namespace App\Provider;

use App\Event\Listener\ContractServiceSubscriber;
use App\Event\Listener\ContractSubscriber;
use App\Event\Listener\DomainEventSubscriber;
use App\Event\Listener\InsuranceCompanySubscriber;
use App\Event\Listener\InsuredPersonSubscriber;
use App\Event\Listener\PersonSubscriber;
use App\Event\Listener\ProvidedServiceSubscriber;
use App\Event\Listener\ServiceSubscriber;
use App\Framework\Provider\AbstractEventServiceProvider;

/**
 * @psalm-api
 */
class EventServiceProvider extends AbstractEventServiceProvider
{
    protected array $listen = [];

    protected array $subscribe = [
        DomainEventSubscriber::class,
        InsuranceCompanySubscriber::class,
        PersonSubscriber::class,
        ServiceSubscriber::class,
        ContractSubscriber::class,
        InsuredPersonSubscriber::class,
        ContractServiceSubscriber::class,
        ProvidedServiceSubscriber::class,
    ];
}
