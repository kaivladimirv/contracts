<?php

declare(strict_types=1);

namespace App\Controller\Api\v1\Debtors;

use App\Framework\Controller\AbstractController;
use App\Framework\Http\JsonResponse;
use App\Framework\Http\ServerRequestInterface;
use App\Model\Contract\Entity\Contract\ContractId;
use App\ReadModel\Contract\Debtor\DebtorFetcherInterface;
use Exception;

class ListDebtorsController extends AbstractController
{
    /**
     * @api        {get} /api/v1/debtors/:contractId Получение списка должников по договору
     * @apiVersion 1.0.0
     * @apiName    Получение списка должников по договору
     * @apiGroup   Debtors
     *
     * @apiParam {String} contractId Уникальный идентификатор договора
     *
     * @apiHeader {String} Authorization Содержит строку формата Bearer {ACCESS_TOKEN}.
     *                                   Тип токена (Bearer) и сам токен доступа.
     *
     * @apiHeaderExample {String} Пример авторизации:
     *                            Authorization: Bearer aa028f85-0771-4d05-8354-b1273b88df77
     *
     * @apiSuccess (200) {String} insuredPersonId Идентификатор застрахованного лица
     * @apiSuccess (200) {String} personId Идентификатор персоны
     * @apiSuccess (200) {String} personLastName Фамилия персоны
     * @apiSuccess (200) {String} personFirstName Имя персоны
     * @apiSuccess (200) {String} personMiddleName Отчество персоны
     * @apiSuccess (200) {String} serviceId Идентификатор услуги
     * @apiSuccess (200) {String} serviceName Название услуги
     * @apiSuccess (200) {Number} debt Задолженность
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     [
     *       {
     *         "insuredPersonId": "ee5e96cb-99b5-4a23-b628-2159d692b07e",
     *         "personId": "be3bdb45-65e2-436b-b4a4-9290176e0af0",
     *         "personLastName": "Иванов",
     *         "personFirstName": "Иван",
     *         "personMiddleName": "Иванович",
     *         "serviceId": "c44e2c7c-a0db-42e0-8ad5-f94f9a3480c0",
     *         "serviceName": "Узи органов брюшной полости",
     *         "debt": 3
     *       },
     *       {
     *         "insuredPersonId": "97776067-d2c2-4773-b47d-e926206a2fa6",
     *         "personId": "e2578ab4-7f79-4f7b-84f8-db96f3919270",
     *         "personLastName": "Сидоров",
     *         "personFirstName": "Алексей",
     *         "personMiddleName": "Алексеевич",
     *         "serviceId": "851ee895-680d-4db7-9b6b-d84f9e9d6a93",
     *         "serviceName": "Первичный прием стоматолога",
     *         "debt": 1000
     *       }
     *     ]
     * @apiError (400) {String} message Текст сообщения об ошибке
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 400 Bad Request
     *     {
     *       "message": ""
     *     }
     */
    public function get(ServerRequestInterface $request, DebtorFetcherInterface $debtorFetcher): JsonResponse
    {
        try {
            $contractId = $request->getAttribute('contractId');

            $debtors = $debtorFetcher->get(new ContractId($contractId));

            return $this->createSuccessJsonResponse($debtors);
        } catch (Exception $e) {
            return $this->createFailJsonResponse(['message' => $e->getMessage()]);
        }
    }
}
