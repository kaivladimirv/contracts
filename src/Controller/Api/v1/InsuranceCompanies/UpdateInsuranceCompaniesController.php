<?php

declare(strict_types=1);

namespace App\Controller\Api\v1\InsuranceCompanies;

use App\Framework\Controller\AbstractController;
use App\Framework\Http\JsonResponse;
use App\Framework\Http\ServerRequestInterface;
use App\Model\InsuranceCompany\Entity\InsuranceCompany;
use App\Model\InsuranceCompany\Exception\InsuranceCompanyNotFoundException;
use App\Model\InsuranceCompany\UseCase\Update\UpdateInsuranceCompanyCommand;
use App\Model\InsuranceCompany\UseCase\Update\UpdateInsuranceCompanyForm;
use App\Model\InsuranceCompany\UseCase\Update\UpdateInsuranceCompanyHandler;
use App\Service\Hydrator\HydratorInterface;
use Exception;
use InvalidArgumentException;

class UpdateInsuranceCompaniesController extends AbstractController
{
    public function __construct(private readonly HydratorInterface $hydrator)
    {
    }

    /**
     * @api        {post} /api/v1/insurance_companies/update Изменение данных о компании
     * @apiVersion 1.0.0
     * @apiName    Изменение данных о компании
     * @apiGroup   Insurance company
     *
     * @apiHeader {String} Authorization Содержит строку формата Bearer {ACCESS_TOKEN}.
     *                                   Тип токена (Bearer) и сам токен доступа.
     *
     * @apiHeaderExample {String} Пример авторизации:
     *                            Authorization: Bearer aa028f85-0771-4d05-8354-b1273b88df77
     *
     * @apiBody {String} name Название компании
     *
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {}
     * @apiError (400) {String} message Текст сообщения об ошибке
     * @apiError (404) {String} message Текст сообщения об ошибке
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "message": "Страховая компания не найден"
     *     }
 */
    public function update(ServerRequestInterface $request, UpdateInsuranceCompanyHandler $handler): JsonResponse
    {
        try {
            /* @var InsuranceCompany $insuranceCompany */
            $insuranceCompany = $request->getAttribute('insuranceCompany');

            $form = new UpdateInsuranceCompanyForm();
            $form->handleRequest($request);

            if (!$form->isValid()) {
                throw new InvalidArgumentException($form->getErrorMessage());
            }

            $formData = array_merge($form->getValidData(), ['id' => $insuranceCompany->getId()->getValue()]);

            /* @var UpdateInsuranceCompanyCommand $command */
            $command = $this->hydrator->hydrate(UpdateInsuranceCompanyCommand::class, $formData);

            $handler->handle($command);

            return $this->createSuccessJsonResponse([]);
        } catch (InsuranceCompanyNotFoundException $e) {
            return $this->createNotFoundJsonResponse(['message' => $e->getMessage()]);
        } catch (Exception $e) {
            return $this->createFailJsonResponse(['message' => $e->getMessage()]);
        }
    }
}
