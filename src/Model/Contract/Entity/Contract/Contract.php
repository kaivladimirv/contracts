<?php

declare(strict_types=1);

namespace App\Model\Contract\Entity\Contract;

use App\Model\AggregateRootInterface;
use App\Model\Contract\Event\Contract\ContractCreatedEvent;
use App\Model\Contract\Event\Contract\ContractDeletedEvent;
use App\Model\Contract\Event\Contract\ContractMaxAmountChangedEvent;
use App\Model\Contract\Event\Contract\ContractNumberChangedEvent;
use App\Model\Contract\Event\Contract\ContractPeriodChangedEvent;
use App\Model\EventTrait;
use DateTimeImmutable;
use InvalidArgumentException;

class Contract implements AggregateRootInterface
{
    use EventTrait;

    private string $number;
    private ?Period $period = null;
    private float $maxAmount;
    private bool $isDeleted = false;

    private function __construct(private readonly ContractId $id, string $number, private readonly InsuranceCompanyId $insuranceCompanyId)
    {
        $this->assertNumberIsNotEmpty($number);
        $this->number = $number;
    }

    public static function create(
        ContractId $id,
        string $number,
        InsuranceCompanyId $insuranceCompanyId,
        ?Period $period,
        float $maxAmount
    ): self {
        $contract = new self($id, $number, $insuranceCompanyId);

        $contract->assertMaxAmountIsGreaterThanOrEqualToZero($maxAmount);

        $contract->period = $period;
        $contract->maxAmount = $maxAmount;

        $contract->registerEvent(
            new ContractCreatedEvent(
                $insuranceCompanyId,
                $id,
                $number,
                $period,
                $maxAmount
            )
        );

        return $contract;
    }

    public function getId(): ContractId
    {
        return $this->id;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getInsuranceCompanyId(): InsuranceCompanyId
    {
        return $this->insuranceCompanyId;
    }

    public function getPeriod(): ?Period
    {
        return $this->period;
    }

    public function getMaxAmount(): float
    {
        return $this->maxAmount;
    }

    public function changeNumber(string $newNumber): void
    {
        $this->assertNumberIsNotEmpty($newNumber);

        $oldNumber = $this->number;
        $this->number = $newNumber;

        if ($oldNumber !== $newNumber) {
            $this->registerEvent(
                new ContractNumberChangedEvent(
                    $this->insuranceCompanyId,
                    $this->id,
                    $oldNumber,
                    $newNumber
                )
            );
        }
    }

    private function assertNumberIsNotEmpty(string $number): void
    {
        if (empty($number)) {
            throw new InvalidArgumentException('Не указан номер договора');
        }
    }

    public function changePeriod(Period $newPeriod): void
    {
        $oldPeriod = $this->period;
        $this->period = $newPeriod;

        if (!$oldPeriod or !$oldPeriod->isEqual($newPeriod)) {
            $this->registerEvent(
                new ContractPeriodChangedEvent(
                    $this->insuranceCompanyId,
                    $this->id,
                    $oldPeriod,
                    $newPeriod
                )
            );
        }
    }

    public function changeMaxAmount(float $newMaxAmount): void
    {
        $this->assertMaxAmountIsGreaterThanOrEqualToZero($newMaxAmount);

        $oldMaxAmount = $this->maxAmount;
        $this->maxAmount = $newMaxAmount;

        if ($oldMaxAmount !== $newMaxAmount) {
            $this->registerEvent(
                new ContractMaxAmountChangedEvent(
                    $this->insuranceCompanyId,
                    $this->id,
                    $oldMaxAmount,
                    $newMaxAmount
                )
            );
        }
    }

    private function assertMaxAmountIsGreaterThanOrEqualToZero(float $maxAmount): void
    {
        if ($maxAmount < 0) {
            throw new InvalidArgumentException('Максимальная сумма по договору не может быть отрицательным значением');
        }
    }

    public function isExpiredTo(DateTimeImmutable $date): bool
    {
        return $this->period->getEndDate() < $date;
    }

    public function delete(): void
    {
        $isAlreadyDeleted = $this->isDeleted;
        $this->isDeleted = true;

        if (!$isAlreadyDeleted) {
            $this->registerEvent(
                new ContractDeletedEvent(
                    $this->insuranceCompanyId,
                    $this->getId(),
                    $this->number
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
            'number'               => $this->number,
            'insurance_company_id' => $this->insuranceCompanyId->getValue(),
            'start_date'           => $this->period->getStartDate()->format('c'),
            'end_date'             => $this->period->getEndDate()->format('c'),
            'max_amount'           => $this->maxAmount,
        ];
    }
}
