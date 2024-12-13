<?php

declare(strict_types=1);

namespace App\Model\Person\Entity;

use App\Model\AggregateRootInterface;
use App\Model\EventTrait;
use App\Model\Person\Event\PersonAddedEvent;
use App\Model\Person\Event\PersonDeletedEvent;
use App\Model\Person\Event\PersonEmailChangedEvent;
use App\Model\Person\Event\PersonNameChangedEvent;
use App\Model\Person\Event\PersonNotifierChangedEvent;
use App\Model\Person\Event\PersonPhoneNumberChangedEvent;
use DomainException;

class Person implements AggregateRootInterface
{
    use EventTrait;

    private ?Email $email = null;
    private ?PhoneNumber $phoneNumber = null;
    private ?string $telegramUserId = null;
    private ?NotifierType $notifierType;
    private bool $isDeleted = false;

    private function __construct(private readonly PersonId $id, private Name $name, private readonly InsuranceCompanyId $insuranceCompanyId)
    {
        $this->notifierType = NotifierType::email();
    }

    public static function create(
        PersonId $id,
        Name $name,
        InsuranceCompanyId $insuranceCompanyId,
        ?Email $email,
        ?PhoneNumber $phoneNumber,
        ?NotifierType $notifierType
    ): self {
        $person = new self($id, $name, $insuranceCompanyId);

        $person->email = $email;
        $person->phoneNumber = $phoneNumber;

        $person->assertEmailIsSpecifiedForNotification($notifierType);
        $person->assertPhoneNumberIsSpecifiedForNotification($notifierType);
        $person->notifierType = $notifierType;

        $person->registerEvent(
            new PersonAddedEvent(
                $person->insuranceCompanyId,
                $person->id,
                $person->name,
                $person->email,
                $person->phoneNumber,
                $person->notifierType
            )
        );

        return $person;
    }

    public function getId(): PersonId
    {
        return $this->id;
    }

    public function getName(): Name
    {
        return $this->name;
    }

    public function getInsuranceCompanyId(): InsuranceCompanyId
    {
        return $this->insuranceCompanyId;
    }

    public function getEmail(): ?Email
    {
        return $this->email;
    }

    public function getPhoneNumber(): ?PhoneNumber
    {
        return $this->phoneNumber;
    }

    public function getTelegramUserId(): ?string
    {
        return $this->telegramUserId;
    }

    public function changeName(Name $newName): void
    {
        $oldName = $this->name;
        $this->name = $newName;

        if (!$oldName->isEqual($newName)) {
            $this->registerEvent(
                new PersonNameChangedEvent(
                    $this->insuranceCompanyId,
                    $this->getId(),
                    $oldName,
                    $newName
                )
            );
        }
    }

    public function getNotifierType(): ?NotifierType
    {
        return $this->notifierType;
    }

    public function shouldBeNotified(): bool
    {
        return !is_null($this->notifierType);
    }

    public function changeEmail(Email $newEmail): void
    {
        $oldEmail = $this->email ?? null;
        $this->email = $newEmail;

        if (empty($oldEmail) or !$oldEmail->isEqual($newEmail)) {
            $this->registerEvent(
                new PersonEmailChangedEvent(
                    $this->insuranceCompanyId,
                    $this->getId(),
                    $oldEmail,
                    $newEmail,
                )
            );
        }
    }

    public function deleteEmail(): void
    {
        $newEmail = null;
        $oldEmail = $this->email;
        $this->email = $newEmail;

        if (!is_null($oldEmail)) {
            $this->registerEvent(
                new PersonEmailChangedEvent(
                    $this->insuranceCompanyId,
                    $this->getId(),
                    $oldEmail,
                    $newEmail,
                )
            );
        }
    }

    public function changePhoneNumber(PhoneNumber $newPhoneNumber): void
    {
        $oldPhoneNumber = $this->phoneNumber ?? null;
        $this->phoneNumber = $newPhoneNumber;

        if (empty($oldPhoneNumber) or !$oldPhoneNumber->isEqual($newPhoneNumber)) {
            $this->telegramUserId = null;

            $this->registerEvent(
                new PersonPhoneNumberChangedEvent(
                    $this->insuranceCompanyId,
                    $this->getId(),
                    $oldPhoneNumber,
                    $newPhoneNumber,
                )
            );
        }
    }

    public function deletePhoneNumber(): void
    {
        $newPhoneNumber = null;
        $oldPhoneNumber = $this->phoneNumber;
        $this->phoneNumber = $newPhoneNumber;

        if (!is_null($oldPhoneNumber)) {
            $this->registerEvent(
                new PersonPhoneNumberChangedEvent(
                    $this->insuranceCompanyId,
                    $this->getId(),
                    $oldPhoneNumber,
                    $newPhoneNumber,
                )
            );
        }
    }

    public function changeNotifierType(NotifierType $newNotifierType): void
    {
        $this->assertEmailIsSpecifiedForNotification($newNotifierType);
        $this->assertPhoneNumberIsSpecifiedForNotification($newNotifierType);

        $oldNotifierType = $this->notifierType ?? null;
        $this->notifierType = $newNotifierType;

        if (is_null($oldNotifierType) or !$oldNotifierType->isEqual($newNotifierType)) {
            $this->registerEvent(
                new PersonNotifierChangedEvent(
                    $this->insuranceCompanyId,
                    $this->getId(),
                    $oldNotifierType,
                    $newNotifierType,
                )
            );
        }
    }

    private function assertEmailIsSpecifiedForNotification(?NotifierType $notifierType): void
    {
        if ($notifierType and $notifierType->isEmail() and is_null($this->email)) {
            throw new DomainException('Для уведомлений по электронной почте необходимо указать email');
        }
    }

    private function assertPhoneNumberIsSpecifiedForNotification(?NotifierType $notifierType): void
    {
        if ($notifierType and $notifierType->isTelegram() and is_null($this->phoneNumber)) {
            throw new DomainException('Для уведомлений через телеграм необходимо указать номер телефона');
        }
    }

    public function deleteNotifierType(): void
    {
        $newNotifierType = null;
        $oldNotifierType = $this->notifierType ?? null;
        $this->notifierType = $newNotifierType;

        if (!is_null($oldNotifierType)) {
            $this->registerEvent(
                new PersonNotifierChangedEvent(
                    $this->insuranceCompanyId,
                    $this->getId(),
                    $oldNotifierType,
                    $newNotifierType,
                )
            );
        }
    }

    public function delete(): void
    {
        $isAlreadyDeleted = $this->isDeleted;
        $this->isDeleted = true;

        if (!$isAlreadyDeleted) {
            $this->registerEvent(
                new PersonDeletedEvent(
                    $this->insuranceCompanyId,
                    $this->getId(),
                    $this->name
                )
            );
        }
    }

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function toArray(): array
    {
        return [
            'id'                   => $this->id->getValue(),
            'last_name'            => $this->name->getLastName(),
            'first_name'           => $this->name->getFirstName(),
            'middle_name'          => $this->name->getMiddleName(),
            'insurance_company_id' => $this->insuranceCompanyId->getValue(),
            'email'                => !empty($this->email) ? $this->email->getValue() : null,
            'phone_number'         => !empty($this->phoneNumber) ? $this->phoneNumber->getValue() : null,
            'telegram_user_id'     => !empty($this->telegramUserId) ? $this->telegramUserId : null,
            'notifier_type'        => !is_null($this->notifierType) ? $this->notifierType->getValue() : null,
        ];
    }
}
