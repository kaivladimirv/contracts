<?php

declare(strict_types=1);

namespace App\Controller\Api\v1\InsuranceCompanies;

use App\Framework\Controller\AbstractController;
use App\Framework\Http\JsonResponse;
use App\Framework\Http\ServerRequestInterface;
use App\Model\InsuranceCompany\Entity\InsuranceCompany;
use App\Model\InsuranceCompany\Exception\InsuranceCompanyNotFoundException;
use App\Model\InsuranceCompany\UseCase\Delete\DeleteInsuranceCompanyCommand;
use App\Model\InsuranceCompany\UseCase\Delete\DeleteInsuranceCompanyHandler;
use App\Service\Hydrator\HydratorInterface;
use Exception;

class DeleteInsuranceCompaniesController extends AbstractController
{
    public function __construct(private readonly HydratorInterface $hydrator)
    {
    }

    /**
     * @api        {delete} /api/v1/insurance_companies/delete Удаление компании
     * @apiVersion 1.0.0
     * @apiName    Удаление компании
     * @apiGroup   Insurance company
     *
     * @apiHeader {String} Authorization Содержит строку формата Bearer {ACCESS_TOKEN}.
     *                                   Тип токена (Bearer) и сам токен доступа.
     *
     * @apiHeaderExample {String} Пример авторизации:
     *                            Authorization: Bearer aa028f85-0771-4d05-8354-b1273b88df77
     *
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {}
     * @apiError (400) {String} message Текст сообщения об ошибке
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 400 Bad Request
     *     {
     *       "message": ""
     *     }
     */
    public function delete(ServerRequestInterface $request, DeleteInsuranceCompanyHandler $handler): JsonResponse
    {
        try {
            /* @var InsuranceCompany $insuranceCompany */
            $insuranceCompany = $request->getAttribute('insuranceCompany');

            $command = $this->hydrator->hydrate(
                DeleteInsuranceCompanyCommand::class,
                ['id' => $insuranceCompany->getId()->getValue()]
            );

            $handler->handle($command);

            return $this->createSuccessJsonResponse([]);
        } catch (InsuranceCompanyNotFoundException) {
            return $this->createSuccessJsonResponse([]);
        } catch (Exception $e) {
            return $this->createFailJsonResponse(['message' => $e->getMessage()]);
        }
    }
}
