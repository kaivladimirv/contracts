<?php

declare(strict_types=1);

namespace App\Controller\Api\v1\Persons;

use App\Framework\Controller\AbstractController;
use App\Framework\Http\JsonResponse;
use App\Framework\Http\ServerRequestInterface;
use App\Model\InsuranceCompany\Entity\InsuranceCompany;
use App\Model\Person\Entity\InsuranceCompanyId;
use App\ReadModel\Person\Filter;
use App\ReadModel\Person\PersonFetcherInterface;
use Exception;

class ListPersonsController extends AbstractController
{
    private const int LIMIT = 20;

    /**
     * @api        {get} /api/v1/persons Получение списка персон
     * @apiVersion 1.0.0
     * @apiName    Получение списка персон
     * @apiGroup   Person
     *
     * @apiHeader {String} Authorization Содержит строку формата Bearer {ACCESS_TOKEN}.
     *                                   Тип токена (Bearer) и сам токен доступа.
     *
     * @apiHeaderExample {String} Пример авторизации:
     *                            Authorization: Bearer aa028f85-0771-4d05-8354-b1273b88df77
     *
     * @apiQuery {Number} [page=1] Номер страницы
     * @apiQuery {String} [name] ФИО персоны
     * @apiQuery {String} [email] Адрес электронной почты
     * @apiQuery {String} [phoneNumber] Номер мобильного телефона
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
     *     [
     *       {
     *         "id": "be3bdb45-65e2-436b-b4a4-9290176e0af0",
     *         "last_name": "Иванов",
     *         "first_name": "Иван",
     *         "middle_name": "Иванович",
     *         "insurance_company_id": "342bc567-f4c5-4f27-90d4-7094a76144cd",
     *         "email": "ivanov_ivan@test.com",
     *         "phone_number": "77776775559",
     *         "telegram_user_id": null,
     *         "notifier_type": 1
     *       },
     *       {
     *         "id": "as7bdf48-65e2-436b-b4a4-9290176e0af0",
     *         "last_name": "Петров",
     *         "first_name": "Петр",
     *         "middle_name": "Петрович",
     *         "insurance_company_id": "342bc567-f4c5-4f27-90d4-7094a76144cd",
     *         "email": "petr_pp@test.com",
     *         "phone_number": "77776775560",
     *         "telegram_user_id": null,
     *         "notifier_type": 0
     *       }
     *     ]
     * @apiError (400) {String} message Текст сообщения об ошибке
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 400 Bad Request
     *     {
     *       "message": ""
     *     }
     */
    public function get(ServerRequestInterface $request, PersonFetcherInterface $personFetcher): JsonResponse
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
            $filter->email = $request->getQueryParam('email');
            $filter->phoneNumber = $request->getQueryParam('phoneNumber');

            $persons = $personFetcher->getAll($insuranceCompanyId, self::LIMIT, $skip, $filter);

            return $this->createSuccessJsonResponse($persons);
        } catch (Exception $e) {
            return $this->createFailJsonResponse(['message' => $e->getMessage()]);
        }
    }
}
