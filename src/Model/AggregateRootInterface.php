<?php

declare(strict_types=1);

namespace App\Model;

interface AggregateRootInterface
{
    public function releaseEvents(): array;
}
