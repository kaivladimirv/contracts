<?php

declare(strict_types=1);

namespace App\Event\Listener;

use Override;
use App\Model\Contract\Service\Contract\CacheItemKeyGenerator;
use App\Model\Contract\Event\Contract\ContractNumberChangedEvent;
use App\Service\Cache\CacheItemPoolInterface;
use App\Event\Dispatcher\EventSubscriberInterface;

readonly class ContractSubscriber implements EventSubscriberInterface
{
    public function __construct(private CacheItemPoolInterface $cache, private CacheItemKeyGenerator $cacheItemKeyGenerator)
    {
    }

    #[Override]
    public function getSubscribedEvents(): array
    {
        return [
            ContractNumberChangedEvent::class => 'onNameChanged',
        ];
    }

    public function onNameChanged(ContractNumberChangedEvent $event): void
    {
        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateFromNumber($event->getInsuranceCompanyId(), $event->getOldNumber()));
    }
}
