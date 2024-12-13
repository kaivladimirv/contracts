<?php

declare(strict_types=1);

namespace App\Service\Sender\Telegram;

use App\Service\Sender\RecipientInterface;

interface TelegramRecipientInterface extends RecipientInterface
{
    public function getUserId(): string;

    public function getPhoneNumber(): string;

    public function getFirstName(): string;

    public function getLastName(): string;
}
