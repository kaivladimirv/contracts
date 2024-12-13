<?php

declare(strict_types=1);

namespace App\Framework\Http;

interface RequestInterface extends MessageInterface
{
    public function getMethod(): string;

    public function getUriPath(): string;
}
