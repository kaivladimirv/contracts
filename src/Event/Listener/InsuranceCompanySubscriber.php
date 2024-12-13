<?php

declare(strict_types=1);

namespace App\Event\Listener;

use Override;
use App\Model\InsuranceCompany\Event\CompanyAccessTokenChangedEvent;
use App\Model\InsuranceCompany\Event\CompanyEmailChangedEvent;
use App\Model\InsuranceCompany\Event\CompanyNameChangedEvent;
use App\Model\InsuranceCompany\Service\CacheItemKeyGenerator;
use App\Service\Cache\CacheItemPoolInterface;
use App\Event\Dispatcher\EventSubscriberInterface;

readonly class InsuranceCompanySubscriber implements EventSubscriberInterface
{
    public function __construct(private CacheItemPoolInterface $cache, private CacheItemKeyGenerator $cacheItemKeyGenerator)
    {
    }

    #[Override]
    public function getSubscribedEvents(): array
    {
        return [
            CompanyEmailChangedEvent::class       => 'onEmailChanged',
            CompanyNameChangedEvent::class        => 'onNameChanged',
            CompanyAccessTokenChangedEvent::class => 'onAccessTokenChanged',
        ];
    }

    public function onEmailChanged(CompanyEmailChangedEvent $event): void
    {
        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateFromEmail($event->getOldEmail()->getValue()));
    }

    public function onNameChanged(CompanyNameChangedEvent $event): void
    {
        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateFromName($event->getOldName()));
    }

    public function onAccessTokenChanged(CompanyAccessTokenChangedEvent $event): void
    {
        if ($event->getOldAccessToken()) {
            $this->cache->deleteItem(
                $this->cacheItemKeyGenerator->generateFromAccessToken($event->getOldAccessToken()->getToken())
            );
        }

        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateFromEmail($event->getEmail()->getValue()));
    }
}
