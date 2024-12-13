<?php

declare(strict_types=1);

namespace App\Controller\Api\v1\InsuranceCompanies;

use App\Framework\Controller\AbstractController;
use App\Framework\Http\JsonResponse;
use App\Framework\Http\ServerRequestInterface;
use App\Model\InsuranceCompany\Repository\InsuranceCompanyRepositoryInterface;
use Exception;

class ListInsuranceCompaniesController extends AbstractController
{
    private const int LIMIT = 20;

    /**
     * @api        {get} /api/v1/insurance_companies Получение списка компаний
     * @apiVersion 1.0.0
     * @apiName    Получение списка компаний
     * @apiGroup   Insurance company
     *
     * @apiHeader {String} Authorization Содержит строку формата Bearer {ACCESS_TOKEN}.
     *                                   Тип токена (Bearer) и сам токен доступа.
     *
     * @apiHeaderExample {String} Пример авторизации:
     *                            Authorization: Bearer aa028f85-0771-4d05-8354-b1273b88df77
     *
     * @apiQuery {Number} [page=1] Номер страницы
     *
     * @apiSuccess (200) {String} id Идентификатор компании
     * @apiSuccess (200) {String} name Название компании
     * @apiSuccess (200) {String} email Электронный адрес компании
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     [
     *       {
     *         "id": "3112762e-ee83-4ebc-988b-4c582ecd0f46",
     *         "name": "Company #1",
     *         "email": "company1@app.test"
     *       },
     *       {
     *         "id": "5795762e-ee83-1ubx-275s-4c582ecd0f99",
     *         "name": "Company #2",
     *         "email": "company2@app.test"
     *       }
     *     ]
     * @apiError (400) {String} message Текст сообщения об ошибке
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 400 Bad Request
     *     {
     *       "message": ""
     *     }
     */
    public function get(ServerRequestInterface $request, InsuranceCompanyRepositoryInterface $repository): JsonResponse
    {
        try {
            $page = $request->getQueryParam('page');
            $page = $page ? : 1;
            $skip = self::LIMIT * ($page - 1);

            $insuranceCompanies = $repository->get(self::LIMIT, $skip);

            return $this->createSuccessJsonResponse(
                $insuranceCompanies->toArray()
            );
        } catch (Exception $e) {
            return $this->createFailJsonResponse(['message' => $e->getMessage()]);
        }
    }
}
