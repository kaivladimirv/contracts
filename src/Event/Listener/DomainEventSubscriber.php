<?php

declare(strict_types=1);

namespace App\Event\Listener;

use Override;
use App\Framework\DIContainer\ContainerInterface;
use App\Model\Contract\Event\Contract\ContractCreatedEvent;
use App\Model\Contract\Event\Contract\ContractDeletedEvent;
use App\Model\Contract\Event\Contract\ContractMaxAmountChangedEvent;
use App\Model\Contract\Event\Contract\ContractNumberChangedEvent;
use App\Model\Contract\Event\Contract\ContractPeriodChangedEvent;
use App\Model\Contract\Event\ContractService\ContractServiceAddedEvent;
use App\Model\Contract\Event\ContractService\ContractServiceDeletedEvent;
use App\Model\Contract\Event\ContractService\ContractServiceLimitChangedEvent;
use App\Model\Contract\Event\InsuredPerson\ExceedLimitAllowedEvent;
use App\Model\Contract\Event\InsuredPerson\ExceedLimitDisallowedEvent;
use App\Model\Contract\Event\InsuredPerson\InsuredPersonAddedEvent;
use App\Model\Contract\Event\InsuredPerson\InsuredPersonDeletedEvent;
use App\Model\Contract\Event\InsuredPerson\PolicyNumberChangedEvent;
use App\Model\Contract\Event\ProvidedService\ProvidedServiceCanceledEvent;
use App\Model\Contract\Event\ProvidedService\ProvidedServiceRegisteredEvent;
use App\Model\DomainEventInterface;
use App\Model\InsuranceCompany\Entity\InsuranceCompanyId;
use App\Model\InsuranceCompany\Event\CompanyAccessTokenChangedEvent;
use App\Model\InsuranceCompany\Event\CompanyDeletedEvent;
use App\Model\InsuranceCompany\Event\CompanyEmailChangedEvent;
use App\Model\InsuranceCompany\Event\CompanyNameChangedEvent;
use App\Model\InsuranceCompany\Event\CompanyRegisteredEvent;
use App\Model\InsuranceCompany\Event\CompanyRegistrationConfirmedEvent;
use App\Model\Person\Event\PersonAddedEvent;
use App\Model\Person\Event\PersonDeletedEvent;
use App\Model\Person\Event\PersonEmailChangedEvent;
use App\Model\Person\Event\PersonNameChangedEvent;
use App\Model\Person\Event\PersonNotifierChangedEvent;
use App\Model\Person\Event\PersonPhoneNumberChangedEvent;
use App\Event\Dispatcher\EventSubscriberInterface;
use App\Model\Service\Event\ServiceAddedEvent;
use App\Model\Service\Event\ServiceDeletedEvent;
use App\Model\Service\Event\ServiceNameChangedEvent;
use App\Service\LoggerActivity\LoggerActivityInterface;
use App\Service\LoggerActivity\LogType;

readonly class DomainEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private ContainerInterface $container)
    {
    }

    #[Override]
    public function getSubscribedEvents(): array
    {
        return [
            CompanyRegisteredEvent::class            => 'onHappened',
            CompanyDeletedEvent::class               => 'onHappened',
            CompanyRegistrationConfirmedEvent::class => 'onHappened',
            CompanyAccessTokenChangedEvent::class    => 'onHappened',
            CompanyEmailChangedEvent::class          => 'onHappened',
            CompanyNameChangedEvent::class           => 'onHappened',

            PersonAddedEvent::class              => 'onHappened',
            PersonDeletedEvent::class            => 'onHappened',
            PersonNameChangedEvent::class        => 'onHappened',
            PersonPhoneNumberChangedEvent::class => 'onHappened',
            PersonEmailChangedEvent::class       => 'onHappened',
            PersonNotifierChangedEvent::class    => 'onHappened',

            ServiceAddedEvent::class       => 'onHappened',
            ServiceDeletedEvent::class     => 'onHappened',
            ServiceNameChangedEvent::class => 'onHappened',

            ContractCreatedEvent::class          => 'onHappened',
            ContractDeletedEvent::class          => 'onHappened',
            ContractNumberChangedEvent::class    => 'onHappened',
            ContractPeriodChangedEvent::class    => 'onHappened',
            ContractMaxAmountChangedEvent::class => 'onHappened',

            ContractServiceAddedEvent::class        => 'onHappened',
            ContractServiceDeletedEvent::class      => 'onHappened',
            ContractServiceLimitChangedEvent::class => 'onHappened',

            InsuredPersonAddedEvent::class    => 'onHappened',
            InsuredPersonDeletedEvent::class  => 'onHappened',
            PolicyNumberChangedEvent::class   => 'onHappened',
            ExceedLimitAllowedEvent::class    => 'onHappened',
            ExceedLimitDisallowedEvent::class => 'onHappened',

            ProvidedServiceRegisteredEvent::class => 'onHappened',
            ProvidedServiceCanceledEvent::class   => 'onHappened',
        ];
    }

    public function onHappened(DomainEventInterface $event): void
    {
        $this->defineInsuranceCompanyId($event);

        /* @var LoggerActivityInterface $loggerActivity */
        $loggerActivity = $this->container->get(LoggerActivityInterface::class);

        $loggerActivity
            ->addLogType($this->getLogTypeByEvent($event::class))
            ->addDateTime($event->getDateOccurred())
            ->addData($event->toArray())
            ->log();
    }


    /**
     * @param CompanyRegisteredEvent|CompanyRegistrationConfirmedEvent $event
     */
    private function defineInsuranceCompanyId(DomainEventInterface $event): void
    {
        $shouldDefineInsuranceCompanyId = in_array($event::class, [
            CompanyRegisteredEvent::class,
            CompanyRegistrationConfirmedEvent::class
        ]);

        if ($shouldDefineInsuranceCompanyId) {
            $id = new InsuranceCompanyId($event->getInsuranceCompanyId()->getValue());
            $this->container->set(InsuranceCompanyId::class, $id);
        }
    }

    private function getMappingOEventsWithLogTypes(): array
    {
        return [
            CompanyRegisteredEvent::class            => LogType::COMPANY__REGISTERED,
            CompanyDeletedEvent::class               => LogType::COMPANY__DELETED,
            CompanyRegistrationConfirmedEvent::class => LogType::COMPANY__REGISTRATION_CONFIRMED,
            CompanyAccessTokenChangedEvent::class    => LogType::COMPANY__ACCESS_TOKEN_CHANGED,
            CompanyEmailChangedEvent::class          => LogType::COMPANY__EMAIL_CHANGED,
            CompanyNameChangedEvent::class           => LogType::COMPANY__NAME_CHANGED,

            PersonAddedEvent::class              => LogType::PERSON__ADDED,
            PersonDeletedEvent::class            => LogType::PERSON__DELETED,
            PersonNameChangedEvent::class        => LogType::PERSON__NAME_CHANGED,
            PersonPhoneNumberChangedEvent::class => LogType::PERSON__PHONE_NUMBER_CHANGED,
            PersonEmailChangedEvent::class       => LogType::PERSON__EMAIL_CHANGED,
            PersonNotifierChangedEvent::class    => LogType::PERSON__NOTIFIER_CHANGED,

            ServiceAddedEvent::class       => LogType::SERVICE__ADDED,
            ServiceDeletedEvent::class     => LogType::SERVICE__DELETED,
            ServiceNameChangedEvent::class => LogType::SERVICE__NAME_CHANGED,

            ContractCreatedEvent::class          => LogType::CONTRACT__CREATED,
            ContractDeletedEvent::class          => LogType::CONTRACT__DELETED,
            ContractNumberChangedEvent::class    => LogType::CONTRACT__NUMBER_CHANGED,
            ContractPeriodChangedEvent::class    => LogType::CONTRACT__PERIOD_CHANGED,
            ContractMaxAmountChangedEvent::class => LogType::CONTRACT__MAX_AMOUNT_CHANGED,

            ContractServiceAddedEvent::class        => LogType::CONTRACT_SERVICE__ADDED,
            ContractServiceDeletedEvent::class      => LogType::CONTRACT_SERVICE__DELETED,
            ContractServiceLimitChangedEvent::class => LogType::CONTRACT_SERVICE__LIMIT_CHANGED,

            InsuredPersonAddedEvent::class    => LogType::INSURED_PERSON__ADDED,
            InsuredPersonDeletedEvent::class  => LogType::INSURED_PERSON__DELETED,
            PolicyNumberChangedEvent::class   => LogType::INSURED_PERSON__POLICY_NUMBER_CHANGED,
            ExceedLimitAllowedEvent::class    => LogType::INSURED_PERSON__EXCEED_LIMIT_ALLOWED,
            ExceedLimitDisallowedEvent::class => LogType::INSURED_PERSON__EXCEED_LIMIT_DISALLOWED,

            ProvidedServiceRegisteredEvent::class => LogType::PROVIDED_SERVICE__REGISTERED,
            ProvidedServiceCanceledEvent::class   => LogType::PROVIDED_SERVICE__CANCELED,
        ];
    }

    private function getLogTypeByEvent(string $eventName): ?int
    {
        return $this->getMappingOEventsWithLogTypes()[$eventName] ?? null;
    }
}
