<?php

declare(strict_types=1);

namespace App\Controller\Api\v1\Services;

use App\Framework\Controller\AbstractController;
use App\Framework\Http\JsonResponse;
use App\Framework\Http\ServerRequestInterface;
use App\Model\InsuranceCompany\Entity\InsuranceCompany;
use App\Model\Service\Entity\InsuranceCompanyId;
use App\ReadModel\Service\Filter;
use App\ReadModel\Service\ServiceFetcherInterface;
use Exception;

class ListServicesController extends AbstractController
{
    private const int LIMIT = 20;

    /**
     * @api        {get} /api/v1/services Получение списка услуг
     * @apiVersion 1.0.0
     * @apiName    Получение списка услуг
     * @apiGroup   Service
     *
     * @apiHeader {String} Authorization Содержит строку формата Bearer {ACCESS_TOKEN}.
     *                                   Тип токена (Bearer) и сам токен доступа.
     *
     * @apiHeaderExample {String} Пример авторизации:
     *                            Authorization: Bearer aa028f85-0771-4d05-8354-b1273b88df77
     *
     * @apiQuery {Number} [page=1] Номер страницы
     * @apiQuery {String} [name] Название услуги
     *
     * @apiSuccess (200) {String} id Идентификатор услуги
     * @apiSuccess (200) {String} name Название услуги
     * @apiSuccess (200) {String} insurance_company_id Уникальный идентификатор страховой компании
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     [
     *       {
     *         "id": "86f159ab-218a-442d-91d8-0064909d1e17",
     *         "name": "Первичный прием невролога"
     *         "insurance_company_id": "342bc567-f4c5-4f27-90d4-7094a76144cd"
     *       },
     *       {
     *         "id": "851ee895-680d-4db7-9b6b-d84f9e9d6a93",
     *         "name": "Первичный прием стоматолога",
     *         "insurance_company_id": "342bc567-f4c5-4f27-90d4-7094a76144cd"
     *       }
     *     ]
     * @apiError (400) {String} message Текст сообщения об ошибке
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 400 Bad Request
     *     {
     *       "message": ""
     *     }
     */
    public function get(ServerRequestInterface $request, ServiceFetcherInterface $serviceFetcher): JsonResponse
    {
        try {
            /* @var InsuranceCompany $insuranceCompany */
            $insuranceCompany = $request->getAttribute('insuranceCompany');
            $insuranceCompanyId = new InsuranceCompanyId($insuranceCompany->getId()->getValue());

            $page = $request->getQueryParam('page');
            $page = $page ? : 1;
            $skip = self::LIMIT * ($page - 1);

            $filter = new Filter();
            $filter->name = $request->getQueryParam('name');

            $services = $serviceFetcher->getAll($insuranceCompanyId, self::LIMIT, $skip, $filter);

            return $this->createSuccessJsonResponse($services);
        } catch (Exception $e) {
            return $this->createFailJsonResponse(['message' => $e->getMessage()]);
        }
    }
}
