<?php

declare(strict_types=1);

namespace App\Model\Service\Entity;

use App\Model\AggregateRootInterface;
use App\Model\EventTrait;
use App\Model\Service\Event\ServiceAddedEvent;
use App\Model\Service\Event\ServiceDeletedEvent;
use App\Model\Service\Event\ServiceNameChangedEvent;
use InvalidArgumentException;

class Service implements AggregateRootInterface
{
    use EventTrait;

    private string $name;
    private bool $isDeleted = false;

    private function __construct(private readonly ServiceId $id, string $name, private readonly InsuranceCompanyId $insuranceCompanyId)
    {
        $this->assertNameIsNotEmpty($name);
        $this->name = $name;
    }

    public static function create(ServiceId $id, string $name, InsuranceCompanyId $insuranceCompanyId): self
    {
        $service = new self($id, $name, $insuranceCompanyId);

        $service->registerEvent(
            new ServiceAddedEvent(
                $insuranceCompanyId,
                $id,
                $name
            )
        );

        return $service;
    }

    public function getId(): ServiceId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getInsuranceCompanyId(): InsuranceCompanyId
    {
        return $this->insuranceCompanyId;
    }

    public function changeName(string $newName): void
    {
        $this->assertNameIsNotEmpty($newName);

        $oldName = $this->name;
        $this->name = $newName;

        if ($oldName !== $newName) {
            $this->registerEvent(
                new ServiceNameChangedEvent(
                    $this->insuranceCompanyId,
                    $this->id,
                    $oldName,
                    $newName
                )
            );
        }
    }

    private function assertNameIsNotEmpty(string $name): void
    {
        if (empty($name)) {
            throw new InvalidArgumentException('Не указано название услуги');
        }
    }

    public function delete(): void
    {
        $isAlreadyDeleted = $this->isDeleted;
        $this->isDeleted = true;

        if (!$isAlreadyDeleted) {
            $this->registerEvent(
                new ServiceDeletedEvent(
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
            'name'                 => $this->name,
            'insurance_company_id' => $this->insuranceCompanyId->getValue(),
        ];
    }
}
