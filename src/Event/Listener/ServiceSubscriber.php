<?php

declare(strict_types=1);

namespace App\Event\Listener;

use Override;
use App\Model\Service\Service\CacheItemKeyGenerator;
use App\Model\Service\Event\ServiceNameChangedEvent;
use App\Service\Cache\CacheItemPoolInterface;
use App\Event\Dispatcher\EventSubscriberInterface;

readonly class ServiceSubscriber implements EventSubscriberInterface
{
    public function __construct(private CacheItemPoolInterface $cache, private CacheItemKeyGenerator $cacheItemKeyGenerator)
    {
    }

    #[Override]
    public function getSubscribedEvents(): array
    {
        return [
            ServiceNameChangedEvent::class => 'onNameChanged',
        ];
    }

    public function onNameChanged(ServiceNameChangedEvent $event): void
    {
        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateFromName($event->getInsuranceCompanyId(), $event->getOldName()));
    }
}
