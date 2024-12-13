<?php

declare(strict_types=1);

namespace App\Controller\Api\v1\InsuredPersons;

use App\Framework\Controller\AbstractController;
use App\Framework\Http\JsonResponse;
use App\Framework\Http\ServerRequestInterface;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\Model\Contract\Exception\InsuredPerson\InsuredPersonNotFoundException;
use App\Model\Contract\Repository\InsuredPerson\InsuredPersonRepositoryInterface;
use Exception;

class ShowInsuredPersonsController extends AbstractController
{
    /**
     * @api        {get} /api/v1/insured_persons/:insuredPersonId Получение данных о застрахованном лице
     * @apiVersion 1.0.0
     * @apiName    Получение данных о застрахованном лице
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
     * @apiSuccess (200) {String} id Идентификатор застрахованного лица
     * @apiSuccess (200) {String} contract_id Идентификатор договора
     * @apiSuccess (200) {String} person_id Идентификатор персоны
     * @apiSuccess (200) {String} policy_number Номер полиса
     * @apiSuccess (200) {Number=0,1} is_allowed_to_exceed_limit Разрешение на превышение лимита
     * @apiSuccess (200) {String} person_name ФИО персоны
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "id": "97776067-d2c2-4773-b47d-e926206a2fa6",
     *       "contract_id": "0267197a-c4c3-4a2b-93b5-bb437272f7ef",
     *       "person_id": "e2578ab4-7f79-4f7b-84f8-db96f3919270",
     *       "policy_number": "VMX-2151",
     *       "is_allowed_to_exceed_limit": 0,
     *       "person_name": "Иванов Иван Иванович"
     *     }
     * @apiError (400) {String} message Текст сообщения об ошибке
     * @apiError (404) {String} message Текст сообщения об ошибке
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "message": "Застрахованное лицо не найдено"
     *     }
     */
    public function show(ServerRequestInterface $request, InsuredPersonRepositoryInterface $repository): JsonResponse
    {
        try {
            $insuredPersonId = $request->getAttribute('insuredPersonId');

            $person = $repository->getOne(new InsuredPersonId($insuredPersonId));

            return $this->createSuccessJsonResponse($person->toArray());
        } catch (InsuredPersonNotFoundException $e) {
            return $this->createNotFoundJsonResponse(['message' => $e->getMessage()]);
        } catch (Exception $e) {
            return $this->createFailJsonResponse(['message' => $e->getMessage()]);
        }
    }
}
