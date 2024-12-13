<?php

declare(strict_types=1);

namespace App\Framework\Controller;

use App\Framework\Http\JsonResponse;
use App\Framework\Http\Response;
use App\Framework\Http\ResponseInterface;

class AbstractController
{
    protected function createSuccessResponse(string $content): ResponseInterface
    {
        return new Response(200, $content);
    }

    protected function createSuccessResponseWithoutContent(): ResponseInterface
    {
        return new Response(204, '');
    }

    protected function createFailResponse(string $content): ResponseInterface
    {
        return new Response(400, $content);
    }

    protected function createSuccessJsonResponse($content): JsonResponse
    {
        return new JsonResponse(200, $content);
    }

    protected function createFailJsonResponse($content): JsonResponse
    {
        return new JsonResponse(400, $content);
    }

    protected function createNotFoundJsonResponse($content): JsonResponse
    {
        return new JsonResponse(404, $content);
    }
}
