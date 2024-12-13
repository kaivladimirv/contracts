<?php

declare(strict_types=1);

namespace App\Service\Sender\Telegram;

use Override;
use InvalidArgumentException;

readonly class TelegramRecipient implements TelegramRecipientInterface
{
    private string $phoneNumber;
    private string $userId;

    public function __construct(string $phoneNumber, string $userId, private string $firstName, private string $lastName)
    {
        if (!$phoneNumber and !$userId) {
            throw new InvalidArgumentException('Необходимо указать номер телефона или id пользователя Telegram');
        }

        $this->phoneNumber = $phoneNumber;
        $this->userId = $userId;
    }

    #[Override]
    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    #[Override]
    public function getUserId(): string
    {
        return $this->userId;
    }

    #[Override]
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    #[Override]
    public function getLastName(): string
    {
        return $this->lastName;
    }
}
