<?php

declare(strict_types=1);

namespace App\ReadModel\Contract\ContractService;

class Filter
{
    public ?int $limitType = null;
    public ?string $serviceName = null;
}
