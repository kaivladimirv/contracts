<?php

declare(strict_types=1);

namespace App\Model\Person\UseCase\Delete;

class DeletePersonCommand
{
    public function __construct(public string $id)
    {
    }
}
