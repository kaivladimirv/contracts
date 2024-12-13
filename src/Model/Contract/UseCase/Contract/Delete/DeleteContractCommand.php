<?php

declare(strict_types=1);

namespace App\Model\Contract\UseCase\Contract\Delete;

class DeleteContractCommand
{
    public function __construct(public string $id)
    {
    }
}
