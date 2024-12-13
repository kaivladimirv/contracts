<?php

declare(strict_types=1);

namespace App\Controller\Api\v1\InsuranceCompanies;

use App\Framework\Controller\AbstractController;
use App\Framework\Http\JsonResponse;
use App\Framework\Http\ServerRequestInterface;
use App\Model\InsuranceCompany\Entity\InsuranceCompany;
use App\Model\InsuranceCompany\Exception\InsuranceCompanyNotFoundException;
use App\Model\InsuranceCompany\Repository\InsuranceCompanyRepositoryInterface;
use Exception;

class ShowInsuranceCompaniesController extends AbstractController
{
    /**
     * @api        {get} /api/v1/insurance_companies/show Получение данных о компании
     * @apiVersion 1.0.0
     * @apiName    Получение данных о компании
     * @apiGroup   Insurance company
     *
     * @apiHeader {String} Authorization Содержит строку формата Bearer {ACCESS_TOKEN}.
     *                                   Тип токена (Bearer) и сам токен доступа.
     *
     * @apiHeaderExample {String} Пример авторизации:
     *                            Authorization: Bearer aa028f85-0771-4d05-8354-b1273b88df77
     *
     * @apiSuccess (200) {String} id Идентификатор компании
     * @apiSuccess (200) {String} name Название компании
     * @apiSuccess (200) {String} email Электронный адрес компании
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "id": "3112762e-ee83-4ebc-988b-4c582ecd0f46",
     *       "name": "Company #1",
     *       "email": "company1@app.test"
     *     }
     * @apiError (400) {String} message Текст сообщения об ошибке
     * @apiError (404) {String} message Текст сообщения об ошибке
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "message": "Страховая компания не найден"
     *     }
     */
    public function show(ServerRequestInterface $request, InsuranceCompanyRepositoryInterface $repository): JsonResponse
    {
        try {
            /* @var InsuranceCompany $insuranceCompany */
            $insuranceCompany = $request->getAttribute('insuranceCompany');

            $insuranceCompany = $repository->getOne($insuranceCompany->getId());

            return $this->createSuccessJsonResponse(
                [
                    'id'    => $insuranceCompany->getId()->getValue(),
                    'name'  => $insuranceCompany->getName(),
                    'email' => $insuranceCompany->getEmail()->getValue(),
                ]
            );
        } catch (InsuranceCompanyNotFoundException $e) {
            return $this->createNotFoundJsonResponse(['message' => $e->getMessage()]);
        } catch (Exception $e) {
            return $this->createFailJsonResponse(['message' => $e->getMessage()]);
        }
    }
}
