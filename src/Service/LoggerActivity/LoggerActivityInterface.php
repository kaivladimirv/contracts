<?php

declare(strict_types=1);

namespace App\Service\LoggerActivity;

use DateTimeImmutable;

interface LoggerActivityInterface
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function addLogType(int $logType): self;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function addDateTime(DateTimeImmutable $dateTime): self;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function addData(array $data): self;

    public function log(): void;
}
