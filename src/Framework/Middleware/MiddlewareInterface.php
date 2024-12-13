<?php

declare(strict_types=1);

namespace App\Framework\Middleware;

use App\Framework\Http\ServerRequestInterface;
use App\Framework\Http\ResponseInterface;

interface MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface;
}
