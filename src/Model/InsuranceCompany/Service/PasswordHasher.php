<?php

declare(strict_types=1);

namespace App\Model\InsuranceCompany\Service;

use DomainException;
use Exception;

class PasswordHasher
{
    /**
     * @throws Exception
     */
    public function hash(string $password): string
    {
        $this->assertIsNotEmpty($password);

        return password_hash($password, PASSWORD_ARGON2I);
    }

    private function assertIsNotEmpty(string $password): void
    {
        if (!trim($password)) {
            throw new DomainException('Не указан пароль');
        }
    }

    public function validate(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
