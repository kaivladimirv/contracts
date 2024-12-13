<?php

declare(strict_types=1);

namespace App\Controller\Api\v1\ContractServices;

use App\Framework\Controller\AbstractController;
use App\Framework\Http\JsonResponse;
use App\Framework\Http\ServerRequestInterface;
use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\ContractService\ServiceId;
use App\Model\Contract\Exception\ContractService\ContractServiceNotFoundException;
use App\Model\Contract\Repository\ContractService\ContractServiceRepositoryInterface;
use Exception;

class ShowContractServicesController extends AbstractController
{
    /**
     * @api        {get} /api/v1/contracts/:contractId/services/:serviceId Получение данных об услуге
     * @apiVersion 1.0.0
     * @apiName    Получение данных об услуге
     * @apiGroup   Contract service
     *
     * @apiParam {String} contractId Идентификатор договора
     * @apiParam {String} serviceId Идентификатор услуги
     *
     * @apiHeader {String} Authorization Содержит строку формата Bearer {ACCESS_TOKEN}.
     *                                   Тип токена (Bearer) и сам токен доступа.
     *
     * @apiHeaderExample {String} Пример авторизации:
     *                            Authorization: Bearer aa028f85-0771-4d05-8354-b1273b88df77
     *
     * @apiSuccess (200) {String} id Идентификатор записи
     * @apiSuccess (200) {String} contract_id Идентификатор договора
     * @apiSuccess (200) {String} service_id Идентификатор услуги
     * @apiSuccess (200) {Number} limit_type Тип лимита
     * @apiSuccess (200) {Number} limit_value Значение лимита
     * @apiSuccess (200) {String} service_name Название услуги
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "id": "f634df66-2aed-4204-820b-f13cba40e6eb",
     *       "contract_id": "0267197a-c4c3-4a2b-93b5-bb437272f7ef",
     *       "service_id": "86f159ab-218a-442d-91d8-0064909d1e17",
     *       "limit_type": 1,
     *       "limit_value": "3",
     *       "service_name": "Первичный прием невролога"
     *     }
     * @apiError (400) {String} message Текст сообщения об ошибке
     * @apiError (404) {String} message Текст сообщения об ошибке
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "message": "Услуга не найдена в договоре"
     *     }
     */
    public function show(ServerRequestInterface $request, ContractServiceRepositoryInterface $repository): JsonResponse
    {
        try {
            $contractId = $request->getAttribute('contractId');
            $serviceId = $request->getAttribute('serviceId');

            $contractService = $repository->getOne(
                new ContractId($contractId),
                new ServiceId($serviceId)
            );

            return $this->createSuccessJsonResponse($contractService->toArray());
        } catch (ContractServiceNotFoundException $e) {
            return $this->createNotFoundJsonResponse(['message' => $e->getMessage()]);
        } catch (Exception $e) {
            return $this->createFailJsonResponse(['message' => $e->getMessage()]);
        }
    }
}
