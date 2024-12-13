<?php

declare(strict_types=1);

namespace App\Model;

use DateTimeImmutable;

interface DomainEventInterface
{
    public function getDateOccurred(): DateTimeImmutable;

    public function toArray(): array;
}
