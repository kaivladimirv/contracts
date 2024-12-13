<?php

declare(strict_types=1);

namespace App\Controller\Api\v1\InsuredPersons;

use App\Framework\Controller\AbstractController;
use App\Framework\Http\JsonResponse;
use App\Framework\Http\ServerRequestInterface;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\ReadModel\Contract\Balance\BalanceFetcherInterface;
use Exception;

class ShowBalanceInsuredPersonsController extends AbstractController
{
    /**
     * @api        {get} /api/v1/insured_persons/:insuredPersonId/balance Получение остатков по застрахованному лицу
     * @apiVersion 1.0.0
     * @apiName    Получение остатков по застрахованному лицу
     * @apiGroup   Insured person
     *
     * @apiParam {String} insuredPersonId Идентификатор застрахованного лица
     *
     * @apiHeader {String} Authorization Содержит строку формата Bearer {ACCESS_TOKEN}.
     *                                   Тип токена (Bearer) и сам токен доступа.
     *
     * @apiHeaderExample {String} Пример авторизации:
     *                            Authorization: Bearer aa028f85-0771-4d05-8354-b1273b88df77
     *
     * @apiSuccess (200) {String} service_id Идентификатор услуги
     * @apiSuccess (200) {Number} limit_type Тип лимита
     * @apiSuccess (200) {Number} balance Остаток
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "service_id": "86f159ab-218a-442d-91d8-0064909d1e17",
     *       "limit_type": 1,
     *       "balance": 7
     *     }
     */
    public function show(ServerRequestInterface $request, BalanceFetcherInterface $fetcher): JsonResponse
    {
        try {
            $insuredPersonId = $request->getAttribute('insuredPersonId');

            $balances = $fetcher->getAllByInsuredPersonId(new InsuredPersonId($insuredPersonId));

            return $this->createSuccessJsonResponse($balances->only(['service_id', 'limit_type', 'balance']));
        } catch (Exception $e) {
            return $this->createFailJsonResponse(['message' => $e->getMessage()]);
        }
    }
}
