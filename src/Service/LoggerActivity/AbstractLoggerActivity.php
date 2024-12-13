<?php

declare(strict_types=1);

namespace App\Service\LoggerActivity;

use Override;
use DateTimeImmutable;
use UnexpectedValueException;

abstract class AbstractLoggerActivity implements LoggerActivityInterface
{
    protected ActorId $actorId;
    protected ?int $logType = null;
    protected array $data = [];
    protected ?DateTimeImmutable $dateTime = null;

    #[Override]
    public function addLogType(int $logType): self
    {
        $this->logType = $logType;

        return $this;
    }

    #[Override]
    public function addDateTime(DateTimeImmutable $dateTime): self
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    #[Override]
    public function addData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    #[Override]
    public function log(): void
    {
        if (!$this->logType) {
            throw new UnexpectedValueException('Не указан тип лога');
        }

        if (!$this->dateTime) {
            throw new UnexpectedValueException('Не указаны дата и время');
        }
    }
}
