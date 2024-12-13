<?php

declare(strict_types=1);

namespace App\Model\Person\Entity;

use InvalidArgumentException;

readonly class Name
{
    private string $lastName;
    private string $firstName;
    private string $middleName;

    public function __construct(string $lastName, string $firstName, string $middleName)
    {
        $this->assertLastNameIsNotEmpty($lastName);
        $this->assertFirstNameIsNotEmpty($lastName);
        $this->assertMiddleNameIsNotEmpty($lastName);

        $this->lastName = trim($lastName);
        $this->firstName = trim($firstName);
        $this->middleName = trim($middleName);
    }

    private function assertLastNameIsNotEmpty(string $lastName): void
    {
        if (empty($lastName)) {
            throw new InvalidArgumentException('Не указана фамилия персоны');
        }
    }

    private function assertFirstNameIsNotEmpty(string $firstName): void
    {
        if (empty($firstName)) {
            throw new InvalidArgumentException('Не указано имя персоны');
        }
    }

    private function assertMiddleNameIsNotEmpty(string $middleName): void
    {
        if (empty($middleName)) {
            throw new InvalidArgumentException('Не указано отчество персоны');
        }
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getMiddleName(): string
    {
        return $this->middleName;
    }

    public function getFullName(): string
    {
        return $this->lastName . ' ' . $this->firstName . ' ' . $this->middleName;
    }

    public function isEqual(self $otherName): bool
    {
        return $this->firstName === $otherName->getFirstName()
            and $this->middleName === $otherName->getMiddleName()
            and $this->lastName === $otherName->getLastName();
    }

    public function toArray(): array
    {
        return [
            'last_name'   => $this->getLastName(),
            'first_name'  => $this->getFirstName(),
            'middle_name' => $this->getMiddleName(),
        ];
    }
}
