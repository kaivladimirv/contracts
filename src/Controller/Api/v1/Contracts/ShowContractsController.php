<?php

declare(strict_types=1);

namespace App\Controller\Api\v1\Contracts;

use App\Framework\Controller\AbstractController;
use App\Framework\Http\JsonResponse;
use App\Framework\Http\ServerRequestInterface;
use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Exception\Contract\ContractNotFoundException;
use App\Model\Contract\Repository\Contract\ContractRepositoryInterface;
use Exception;

class ShowContractsController extends AbstractController
{
    /**
     * @api        {get} /api/v1/contracts/:id Получение данных о договоре
     * @apiVersion 1.0.0
     * @apiName    Получение данных о договоре
     * @apiGroup   Contract
     *
     * @apiParam {String} id Уникальный идентификатор договора
     *
     * @apiHeader {String} Authorization Содержит строку формата Bearer {ACCESS_TOKEN}.
     *                                   Тип токена (Bearer) и сам токен доступа.
     *
     * @apiHeaderExample {String} Пример авторизации:
     *                            Authorization: Bearer aa028f85-0771-4d05-8354-b1273b88df77
     *
     * @apiSuccess (200) {String} id Идентификатор договора
     * @apiSuccess (200) {String} number Номер договора
     * @apiSuccess (200) {String} start_date Дата начала действия договора
     * @apiSuccess (200) {String} end_date Дата окончания действия договора
     * @apiSuccess (200) {Number} max_amount Максимальная сумма по договору
     * @apiSuccess (200) {String} insurance_company_id Уникальный идентификатор страховой компании
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "id": "9dc5aa54-34c9-445c-b6ae-2992955d76a4",
     *       "number": "SD-SDS12-SD16",
     *       "start_date": "2021-01-01 00:00:00",
     *       "start_date": "2021-01-01 00:00:00",
     *       "max_amount": "1000",
     *       "insurance_company_id": "342bc567-f4c5-4f27-90d4-7094a76144cd"
     *     }
     * @apiError (400) {String} message Текст сообщения об ошибке
     * @apiError (404) {String} message Текст сообщения об ошибке
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "message": "Договор не найден"
     *     }
     */
    public function show(ServerRequestInterface $request, ContractRepositoryInterface $repository): JsonResponse
    {
        try {
            $id = $request->getAttribute('id');
            $contractId = new ContractId($id);

            $contract = $repository->getOne($contractId);

            return $this->createSuccessJsonResponse($contract->toArray());
        } catch (ContractNotFoundException $e) {
            return $this->createNotFoundJsonResponse(['message' => $e->getMessage()]);
        } catch (Exception $e) {
            return $this->createFailJsonResponse(['message' => $e->getMessage()]);
        }
    }
}
