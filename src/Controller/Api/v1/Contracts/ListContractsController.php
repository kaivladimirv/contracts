<?php

declare(strict_types=1);

namespace App\Controller\Api\v1\Contracts;

use App\Framework\Controller\AbstractController;
use App\Framework\Http\JsonResponse;
use App\Framework\Http\ServerRequestInterface;
use App\Model\Contract\Entity\Contract\InsuranceCompanyId;
use App\Model\InsuranceCompany\Entity\InsuranceCompany;
use App\ReadModel\Contract\Contract\ContractFetcherInterface;
use App\ReadModel\Contract\Contract\Filter\Filter;
use App\ReadModel\Contract\Contract\Filter\FilterForm;
use Exception;
use InvalidArgumentException;

class ListContractsController extends AbstractController
{
    private const int LIMIT = 20;

    /**
     * @api        {get} /api/v1/contracts Получение списка договоров
     * @apiVersion 1.0.0
     * @apiName    Получение списка договоров
     * @apiGroup   Contract
     *
     * @apiHeader {String} Authorization Содержит строку формата Bearer {ACCESS_TOKEN}.
     *                                   Тип токена (Bearer) и сам токен доступа.
     *
     * @apiHeaderExample {String} Пример авторизации:
     *                            Authorization: Bearer aa028f85-0771-4d05-8354-b1273b88df77
     *
     * @apiQuery {Number} [page=1] Номер страницы
     * @apiQuery {String} [number] Номер договора
     *
     * @apiSuccess (200) {String} id Идентификатор договора
     * @apiSuccess (200) {String} number Номер договора
     * @apiSuccess (200) {String} start_date Дата начала действия договора
     * @apiSuccess (200) {String} end_date Дата окончания действия договора
     * @apiSuccess (200) {Number} max_amount Максимальная сумма по договору
     * @apiSuccess (200) {String} insurance_company_id Уникальный идентификатор страховой компании
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     [
     *       {
     *         "id": "9dc5aa54-34c9-445c-b6ae-2992955d76a4",
     *         "number": "SD-SDS12-SD32",
     *         "start_date": "2021-01-01 00:00:00",
     *         "start_date": "2021-11-30 00:00:00",
     *         "max_amount": "1000",
     *         "insurance_company_id": "342bc567-f4c5-4f27-90d4-7094a76144cd"
     *       },
     *       {
     *         "id": "957b76d7-792d-4bb8-8d87-a0fb1e82fe55",
     *         "number": "SDA16-CMV12-KKK771",
     *         "start_date": "2021-01-01 00:00:00",
     *         "start_date": "2021-12-01 00:00:00",
     *         "max_amount": "10000",
     *         "insurance_company_id": "342bc567-f4c5-4f27-90d4-7094a76144cd"
     *       }
     *     ]
     * @apiError (400) {String} message Текст сообщения об ошибке
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 400 Bad Request
     *     {
     *       "message": ""
     *     }
     */
    public function get(ServerRequestInterface $request, ContractFetcherInterface $contractFetcher): JsonResponse
    {
        try {
            /* @var InsuranceCompany $insuranceCompany */
            $insuranceCompany = $request->getAttribute('insuranceCompany');
            $insuranceCompanyId = new InsuranceCompanyId($insuranceCompany->getId()->getValue());

            $form = new FilterForm();
            $form->handleRequest($request);

            if (!$form->isValid()) {
                throw new InvalidArgumentException($form->getErrorMessage());
            }

            $formData = $form->getValidData();

            $filter = new Filter();
            $filter->number = $formData['number'];

            $persons = $contractFetcher->getAll(
                $insuranceCompanyId,
                self::LIMIT,
                self::LIMIT * ($formData['page'] - 1),
                $filter
            );

            return $this->createSuccessJsonResponse($persons);
        } catch (Exception $e) {
            return $this->createFailJsonResponse(['message' => $e->getMessage()]);
        }
    }
}
