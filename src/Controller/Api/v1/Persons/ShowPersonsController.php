<?php

declare(strict_types=1);

namespace App\Controller\Api\v1\Persons;

use App\Framework\Controller\AbstractController;
use App\Framework\Http\JsonResponse;
use App\Framework\Http\ServerRequestInterface;
use App\Model\Person\Entity\PersonId;
use App\Model\Person\Exception\PersonNotFoundException;
use App\Model\Person\Repository\PersonRepositoryInterface;
use Exception;

class ShowPersonsController extends AbstractController
{
    /**
     * @api        {get} /api/v1/persons/:id Получение данных о персоне
     * @apiVersion 1.0.0
     * @apiName    Получение данных о персоне
     * @apiGroup   Person
     *
     * @apiParam {String} id Уникальный идентификатор персоны
     *
     * @apiHeader {String} Authorization Содержит строку формата Bearer {ACCESS_TOKEN}.
     *                                   Тип токена (Bearer) и сам токен доступа.
     *
     * @apiHeaderExample {String} Пример авторизации:
     *                            Authorization: Bearer aa028f85-0771-4d05-8354-b1273b88df77
     *
     * @apiSuccess (200) {String} id Идентификатор персоны
     * @apiSuccess (200) {String} last_name Фамилия персоны
     * @apiSuccess (200) {String} first_name Имя персоны
     * @apiSuccess (200) {String} middle_name Отчество персоны
     * @apiSuccess (200) {String} insurance_company_id Уникальный идентификатор страховой компании
     * @apiSuccess (200) {String} email Адрес электронной почты
     * @apiSuccess (200) {String} phone_number Номер мобильного телефона
     * @apiSuccess (200) {String} telegram_user_id Уникальный идентификатор пользователя telegram
     * @apiSuccess (200) {Number} notifier_type Тип уведомителя
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "id": "be3bdb45-65e2-436b-b4a4-9290176e0af0",
     *       "last_name": "Иванов",
     *       "first_name": "Иван",
     *       "middle_name": "Иванович",
     *       "insurance_company_id": "342bc567-f4c5-4f27-90d4-7094a76144cd",
     *       "email": "ivanov_ivan@test.com",
     *       "phone_number": "77776775559",
     *       "telegram_user_id": null,
     *       "notifier_type": 1
     *     }
     * @apiError (400) {String} message Текст сообщения об ошибке
     * @apiError (404) {String} message Текст сообщения об ошибке
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "message": "Персона не найден"
     *     }
     */
    public function show(ServerRequestInterface $request, PersonRepositoryInterface $repository): JsonResponse
    {
        try {
            $id = $request->getAttribute('id');
            $personId = new PersonId($id);

            $person = $repository->getOne($personId);

            return $this->createSuccessJsonResponse($person->toArray());
        } catch (PersonNotFoundException $e) {
            return $this->createNotFoundJsonResponse(['message' => $e->getMessage()]);
        } catch (Exception $e) {
            return $this->createFailJsonResponse(['message' => $e->getMessage()]);
        }
    }
}
