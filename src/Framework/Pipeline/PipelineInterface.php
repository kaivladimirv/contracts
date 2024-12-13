<?php

declare(strict_types=1);

namespace App\Framework\Pipeline;

use App\Framework\Middleware\MiddlewareInterface;

interface PipelineInterface
{
    public function addPipe(MiddlewareInterface|string $middleware): self;
}
