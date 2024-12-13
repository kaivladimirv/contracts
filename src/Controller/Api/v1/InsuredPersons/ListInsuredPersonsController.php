<?php

declare(strict_types=1);

namespace App\Controller\Api\v1\InsuredPersons;

use App\Framework\Controller\AbstractController;
use App\Framework\Http\JsonResponse;
use App\Framework\Http\ServerRequestInterface;
use App\Model\Contract\Entity\Contract\ContractId;
use App\ReadModel\Contract\InsuredPerson\Filter;
use App\ReadModel\Contract\InsuredPerson\InsuredPersonFetcherInterface;
use Exception;

class ListInsuredPersonsController extends AbstractController
{
    private const int LIMIT = 20;

    /**
     * @api        {get} /api/v1/contracts/:contractId/insured_persons Получение списка застрахованных лиц
     * @apiVersion 1.0.0
     * @apiName    Получение списка застрахованных лиц
     * @apiGroup   Insured person
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
     * @apiQuery {String} [personName] ФИО персоны
     * @apiQuery {String} [policyNumber] Номер полиса
     * @apiQuery {Number=0,1} [isAllowedToExceedLimit] Разрешение на превышение лимита
     *
     * @apiSuccess (200) {String} id Идентификатор застрахованного лица
     * @apiSuccess (200) {String} contract_id Идентификатор договора
     * @apiSuccess (200) {String} person_id Идентификатор персоны
     * @apiSuccess (200) {String} policy_number Номер полиса
     * @apiSuccess (200) {Number=0,1} is_allowed_to_exceed_limit Разрешение на превышение лимита
     * @apiSuccess (200) {String} person_name ФИО персоны
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     [
     *       {
     *         "id": "97776067-d2c2-4773-b47d-e926206a2fa6",
     *         "contract_id": "0267197a-c4c3-4a2b-93b5-bb437272f7ef",
     *         "person_id": "e2578ab4-7f79-4f7b-84f8-db96f3919270",
     *         "policy_number": "VMX-2151",
     *         "is_allowed_to_exceed_limit": 0,
     *         "person_name": "Иванов Иван Иванович"
     *       },
     *       {
     *         "id": "48434f1a-f69f-48c5-8121-483aa2264deb",
     *         "contract_id": "0267197a-c4c3-4a2b-93b5-bb437272f7ef",
     *         "person_id": "1124254c-1027-4c23-b161-98586cd8aef5",
     *         "policy_number": "VMXCV-2152",
     *         "is_allowed_to_exceed_limit": 1,
     *         "person_name": "Петров Петр Петрович"
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
        InsuredPersonFetcherInterface $insuredPersonFetcher
    ): JsonResponse {
        try {
            $contractId = $request->getAttribute('contractId');

            $page = $request->getQueryParam('page');
            $page = $page ? : 1;
            $skip = self::LIMIT * ($page - 1);

            $filter = new Filter();
            $filter->personName = $request->getQueryParam('personName');
            $filter->policyNumber = $request->getQueryParam('policyNumber');

            if (is_numeric($request->getQueryParam('isAllowedToExceedLimit'))) {
                $filter->isAllowedToExceedLimit = (bool) $request->getQueryParam('isAllowedToExceedLimit');
            }

            $persons = $insuredPersonFetcher->getAll(new ContractId($contractId), self::LIMIT, $skip, $filter);

            return $this->createSuccessJsonResponse($persons);
        } catch (Exception $e) {
            return $this->createFailJsonResponse(['message' => $e->getMessage()]);
        }
    }
}
