<?php

declare(strict_types=1);

namespace App\Model\Contract\Entity\Contract;

use DateTimeImmutable;
use InvalidArgumentException;

readonly class Period
{
    private DateTimeImmutable $startDate;
    private DateTimeImmutable $endDate;

    public function __construct(DateTimeImmutable $startDate, DateTimeImmutable $endDate)
    {
        $this->assertStartDateIsLessThanEndDate($startDate, $endDate);

        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function getStartDate(): DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getEndDate(): DateTimeImmutable
    {
        return $this->endDate;
    }

    public function isDateIncluded(DateTimeImmutable $date): bool
    {
        return (($date >= $this->startDate) and ($date <= $this->endDate));
    }

    private function assertStartDateIsLessThanEndDate(
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate
    ): void {
        if ($startDate >= $endDate) {
            throw new InvalidArgumentException('Дата начала договора должна быть меньше даты окончания');
        }
    }

    public function isEqual(self $otherPeriod): bool
    {
        return $this->startDate == $otherPeriod->getStartDate()
            and $this->endDate == $otherPeriod->getEndDate();
    }

    public function toArray(): array
    {
        return [
            'startDate' => $this->getStartDate()->format('c'),
            'endDate'   => $this->getEndDate()->format('c'),
        ];
    }
}
