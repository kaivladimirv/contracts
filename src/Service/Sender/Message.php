<?php

declare(strict_types=1);

namespace App\Service\Sender;

readonly class Message
{
    public function __construct(private string $subject, private string $content)
    {
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
