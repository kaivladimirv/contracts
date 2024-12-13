<?php

declare(strict_types=1);

namespace App\Model\Person\UseCase\Update;

class UpdatePersonCommand
{
    public string $id;
    public string $lastName;
    public string $firstName;
    public string $middleName;
    public string $email;
    public string $phoneNumber;
    public ?int $notifierType = null;
}
