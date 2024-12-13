<?php

declare(strict_types=1);

namespace App\Service\Sender\Mail;

use Override;

readonly class MailRecipient implements MailRecipientInterface
{
    public function __construct(private string $email)
    {
    }

    #[Override]
    public function getEmail(): string
    {
        return $this->email;
    }
}
