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
use App\Model\InsuranceCompany\Entity\Email;
use App\Model\InsuranceCompany\Entity\InsuranceCompanyId;
use App\Model\InsuranceCompany\Repository\InsuranceCompanyRepositoryInterface;
use App\Model\InsuranceCompany\Service\PasswordHasher;

/**
 * @psalm-api
 */
readonly class BasicAuthMiddleware implements MiddlewareInterface
{
    public function __construct(private InsuranceCompanyRepositoryInterface $repository, private PasswordHasher $passwordHasher, private ContainerInterface $container)
    {
    }

    #[Override]
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $email = $request->getServerParam('PHP_AUTH_USER');
        $password = $request->getServerParam('PHP_AUTH_PW');

        if (!$email or !$password) {
            return new JsonResponse(401, ['message' => 'Неверно указан логин или пароль']);
        }

        if (!$insuranceCompany = $this->repository->findOneByEmail(new Email($email))) {
            return new JsonResponse(401, ['message' => 'Неверно указан логин или пароль']);
        }

        if (!$this->passwordHasher->validate($password, $insuranceCompany->getPasswordHash())) {
            return new JsonResponse(401, ['message' => 'Неверно указан логин или пароль']);
        }

        $request = $request->withAttribute('insuranceCompany', $insuranceCompany);

        $this->container->set(InsuranceCompanyId::class, $insuranceCompany->getId());

        return $handler->handle($request);
    }
}
