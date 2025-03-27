<?php

declare(strict_types=1);

namespace App\Middleware;

use Override;
use App\Framework\DIContainer\ContainerInterface;
use App\Framework\Http\JsonResponse;
use App\Framework\Http\ServerRequestInterface;
use App\Framework\Http\ResponseInterface;
use App\Framework\Middleware\MiddlewareInterface;
use App\Framework\Middleware\RequestHandlerInterface;
use App\Model\InsuranceCompany\Entity\InsuranceCompanyId;
use App\Model\InsuranceCompany\Repository\InsuranceCompanyRepositoryInterface;
use DateTimeImmutable;

/**
 * @psalm-api
 */
readonly class TokenAuthMiddleware implements MiddlewareInterface
{
    public function __construct(private InsuranceCompanyRepositoryInterface $repository, private ContainerInterface $container)
    {
    }

    #[Override]
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$accessToken = $this->extractBearerToken($request->getHeader('Authorization'))) {
            return new JsonResponse(401, ['message' => 'Не указан access-токен']);
        }

        if (!$insuranceCompany = $this->repository->findOneByAccessToken($accessToken)) {
            return new JsonResponse(401, ['message' => 'Access-токен не существует']);
        }

        if ($insuranceCompany->getAccessToken()->isExpiredTo(new DateTimeImmutable())) {
            return new JsonResponse(401, ['message' => 'Срок действия токена истек']);
        }

        $request = $request->withAttribute('insuranceCompany', $insuranceCompany);

        $this->container->set(InsuranceCompanyId::class, $insuranceCompany->getId());

        return $handler->handle($request);
    }

    private function extractBearerToken(?string $headerValue): string
    {
        if ($headerValue and stripos($headerValue, 'Bearer') === 0) {
            return substr($headerValue, 7, strlen($headerValue) - 7);
        }

        return '';
    }
}
