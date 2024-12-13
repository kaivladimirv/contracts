<?php

declare(strict_types=1);

namespace App\Service\Queue;

readonly class QueueMessage
{
    public function __construct(private mixed $data)
    {
    }

    public function getBody(): mixed
    {
        return $this->data;
    }
}
