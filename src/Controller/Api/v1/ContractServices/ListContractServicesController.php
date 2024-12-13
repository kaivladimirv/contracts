<?php

declare(strict_types=1);

namespace App\Controller\Api\v1\ContractServices;

use App\Framework\Controller\AbstractController;
use App\Framework\Http\JsonResponse;
use App\Framework\Http\ServerRequestInterface;
use App\Model\Contract\Entity\Contract\ContractId;
use App\ReadModel\Contract\ContractService\ContractServiceFetcherInterface;
use App\ReadModel\Contract\ContractService\Filter;
use Exception;

class ListContractServicesController extends AbstractController
{
    private const int LIMIT = 20;

    /**
     * @api        {get} /api/v1/contracts/:contractId/services Получение списка услуг входящих в договор
     * @apiVersion 1.0.0
     * @apiName    Получение списка услуг входящих в договор
     * @apiGroup   Contract service
     *
     * @apiParam {String} contractId Идентификатор договора
     *
     * @apiHeader {String} Authorization Содержит строку формата Bearer {ACCESS_TOKEN}.
     *                                   Тип токена (Bearer) и сам токен доступа.
     *
     * @apiHeaderExample {String} Пример авторизации:
     *                            Authorization: Bearer aa028f85-0771-4d05-8354-b1273b88df77
     *
     * @apiQuery {Number} [page=1] Номер страницы
     * @apiQuery {Number} [limitType] Тип лимита
     * @apiQuery {String} [serviceName] Название услуги
     *
     * @apiSuccess (200) {String} id Идентификатор записи
     * @apiSuccess (200) {String} contract_id Идентификатор договора
     * @apiSuccess (200) {String} service_id Идентификатор услуги
     * @apiSuccess (200) {Number} limit_type Тип лимита
     * @apiSuccess (200) {Number} limit_value Значение лимита
     * @apiSuccess (200) {String} service_name Название услуги
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     [
     *       {
     *         "id": "f634df66-2aed-4204-820b-f13cba40e6eb",
     *         "contract_id": "0267197a-c4c3-4a2b-93b5-bb437272f7ef",
     *         "service_id": "86f159ab-218a-442d-91d8-0064909d1e17",
     *         "limit_type": 1,
     *         "limit_value": "3",
     *         "service_name": "Первичный прием невролога"
     *       },
     *       {
     *         "id": "55066584-bf6b-42b1-9335-d4d663025c79",
     *         "contract_id": "0267197a-c4c3-4a2b-93b5-bb437272f7ef",
     *         "service_id": "39124ca4-1b77-4700-aba8-3ad062323d7e",
     *         "limit_type": 0,
     *         "limit_value": "120000",
     *         "service_name": "Первичный прием терапевта"
     *       }
     *     ]
     * @apiError (400) {String} message Текст сообщения об ошибке
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 400 Bad Request
     *     {
     *       "message": ""
     *     }
     */
    public function get(
        ServerRequestInterface $request,
        ContractServiceFetcherInterface $contractServiceFetcher
    ): JsonResponse {
        try {
            $contractId = $request->getAttribute('contractId');

            $page = $request->getQueryParam('page');
            $page = $page ? : 1;
            $skip = self::LIMIT * ($page - 1);

            $filter = new Filter();
            if (is_numeric($request->getQueryParam('limitType'))) {
                $filter->limitType = (int) $request->getQueryParam('limitType');
            }

            $filter->serviceName = $request->getQueryParam('serviceName');

            $persons = $contractServiceFetcher->getAll(new ContractId($contractId), self::LIMIT, $skip, $filter);

            return $this->createSuccessJsonResponse($persons);
        } catch (Exception $e) {
            return $this->createFailJsonResponse(['message' => $e->getMessage()]);
        }
    }
}
