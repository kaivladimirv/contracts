<?php

declare(strict_types=1);

namespace App\Model\Service\UseCase\Add;

class AddServiceCommand
{
    public string $id;
    public string $name;
    public string $insuranceCompanyId;
}
