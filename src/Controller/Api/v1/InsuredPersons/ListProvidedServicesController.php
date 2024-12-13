<?php

declare(strict_types=1);

namespace App\Controller\Api\v1\InsuredPersons;

use App\Framework\Controller\AbstractController;
use App\Framework\Http\JsonResponse;
use App\Framework\Http\ServerRequestInterface;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\ReadModel\ProvidedService\Filter;
use App\ReadModel\ProvidedService\ProvidedServiceFetcherInterface;
use Exception;

class ListProvidedServicesController extends AbstractController
{
    private const int LIMIT = 20;

    /**
     * @api        {get} /api/v1/insured_persons/:insuredPersonId/provided_services Получение списка оказанных услуг
     * @apiVersion 1.0.0
     * @apiName    Получение списка оказанных услуг
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
     * @apiQuery {Number} [page=1] Номер страницы
     * @apiQuery {String} [serviceName] Название услуги
     * @apiQuery {String} [startDate] Начальная дата периода (выборка по дате оказания услуги). В формате дд.мм.гггг чч:мм
     * @apiQuery {String} [endDate] Конечная дата периода (выборка по дате оказания услуги). В формате дд.мм.гггг чч:мм
     *
     * @apiSuccess (200) {String} id Идентификатор оказанной услуги
     * @apiSuccess (200) {String} contract_id Идентификатор договора
     * @apiSuccess (200) {String} insured_person_id Идентификатор застрахованного лица
     * @apiSuccess (200) {String} date_of_service Дата оказания услуги
     * @apiSuccess (200) {String} service_id Идентификатор услуги
     * @apiSuccess (200) {String} service_name Название услуги
     * @apiSuccess (200) {Number} limit_type Тип лимита
     * @apiSuccess (200) {Number} quantity Количество оказанной услуги
     * @apiSuccess (200) {Number} price Стоимость оказанной услуги
     * @apiSuccess (200) {Number} amount Сумма оказанной услуги
     * @apiSuccess (200) {Number=0,1} is_deleted Признак удаления

     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     [
     *       {
     *         "id": "596c78ee-325f-416a-b169-bcba36c2a339",
     *         "contract_id": "0267197a-c4c3-4a2b-93b5-bb437272f7ef",
     *         "insured_person_id": "17a35023-e689-49b1-a6a6-580566eee6ec",
     *         "date_of_service": "2021-07-16 00:00:00",
     *         "service_id": "851ee895-680d-4db7-9b6b-d84f9e9d6a93",
     *         "service_name": "Первичный прием стоматолога",
     *         "limit_type": 0,
     *         "quantity": "1",
     *         "price": "15000"
     *         "amount": "15000",
     *         "is_deleted": 0,
     *       },
     *       {
     *         "id": "3f5e916d-d0e9-4463-a29a-87e7190e6bda",
     *         "contract_id": "0267197a-c4c3-4a2b-93b5-bb437272f7ef",
     *         "insured_person_id": "17a35023-e689-49b1-a6a6-580566eee6ec",
     *         "date_of_service": "2021-08-16 00:00:00",
     *         "service_id": "86f159ab-218a-442d-91d8-0064909d1e17",
     *         "service_name": "Первичный прием невролога",
     *         "limit_type": 1,
     *         "quantity": "1",
     *         "price": "10000"
     *         "amount": "10000",
     *         "is_deleted": 0,
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
        ProvidedServiceFetcherInterface $providedServiceFetcher
    ): JsonResponse {
        try {
            $insuredPersonId = $request->getAttribute('insuredPersonId');

            $page = $request->getQueryParam('page');
            $page = $page ? : 1;
            $skip = self::LIMIT * ($page - 1);

            $filter = new Filter();
            $filter->serviceName = $request->getQueryParam('serviceName');
            $filter->startDate = $request->getQueryParam('startDate');
            $filter->endDate = $request->getQueryParam('endDate');

            $providedServices = $providedServiceFetcher->getAllByInsuredPerson(
                new InsuredPersonId($insuredPersonId),
                self::LIMIT,
                $skip,
                $filter
            );

            return $this->createSuccessJsonResponse($providedServices);
        } catch (Exception $e) {
            return $this->createFailJsonResponse(['message' => $e->getMessage()]);
        }
    }
}
