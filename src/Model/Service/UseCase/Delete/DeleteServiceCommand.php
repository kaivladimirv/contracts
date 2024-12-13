<?php

declare(strict_types=1);

namespace App\Model\Service\UseCase\Delete;

class DeleteServiceCommand
{
    public function __construct(public string $id)
    {
    }
}
