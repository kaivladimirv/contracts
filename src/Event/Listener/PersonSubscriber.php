<?php

declare(strict_types=1);

namespace App\Event\Listener;

use Override;
use App\Model\Person\Event\PersonEmailChangedEvent;
use App\Model\Person\Event\PersonNameChangedEvent;
use App\Model\Person\Event\PersonPhoneNumberChangedEvent;
use App\Model\Person\Service\CacheItemKeyGenerator;
use App\Service\Cache\CacheItemPoolInterface;
use App\Event\Dispatcher\EventSubscriberInterface;

readonly class PersonSubscriber implements EventSubscriberInterface
{
    public function __construct(private CacheItemPoolInterface $cache, private CacheItemKeyGenerator $cacheItemKeyGenerator)
    {
    }

    #[Override]
    public function getSubscribedEvents(): array
    {
        return [
            PersonNameChangedEvent::class        => 'onNameChanged',
            PersonEmailChangedEvent::class       => 'onEmailChanged',
            PersonPhoneNumberChangedEvent::class => 'onPhoneNumberChanged',
        ];
    }

    public function onNameChanged(PersonNameChangedEvent $event): void
    {
        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateFromName($event->getInsuranceCompanyId(), $event->getOldName()));
    }

    public function onEmailChanged(PersonEmailChangedEvent $event): void
    {
        if ($email = $event->getOldEmail()) {
            $this->cache->deleteItem($this->cacheItemKeyGenerator->generateFromEmail($event->getInsuranceCompanyId(), $email));
        }
    }

    public function onPhoneNumberChanged(PersonPhoneNumberChangedEvent $event): void
    {
        if ($phoneNumber = $event->getOldPhoneNumber()) {
            $this->cache->deleteItem($this->cacheItemKeyGenerator->generateFromPhoneNumber($event->getInsuranceCompanyId(), $phoneNumber));
        }
    }
}
