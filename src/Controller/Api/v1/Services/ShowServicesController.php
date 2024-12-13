<?php

declare(strict_types=1);

namespace App\Controller\Api\v1\Services;

use App\Framework\Controller\AbstractController;
use App\Framework\Http\JsonResponse;
use App\Framework\Http\ServerRequestInterface;
use App\Model\Service\Entity\ServiceId;
use App\Model\Service\Exception\ServiceNotFoundException;
use App\Model\Service\Repository\ServiceRepositoryInterface;
use Exception;

class ShowServicesController extends AbstractController
{
    /**
     * @api        {get} /api/v1/services/:id Получение данных об услуге
     * @apiVersion 1.0.0
     * @apiName    Получение данных об услуге
     * @apiGroup   Service
     *
     * @apiParam {String} id Уникальный идентификатор услуги
     *
     * @apiHeader {String} Authorization Содержит строку формата Bearer {ACCESS_TOKEN}.
     *                                   Тип токена (Bearer) и сам токен доступа.
     *
     * @apiHeaderExample {String} Пример авторизации:
     *                            Authorization: Bearer aa028f85-0771-4d05-8354-b1273b88df77
     *
     * @apiSuccess (200) {String} id Идентификатор услуги
     * @apiSuccess (200) {String} name Название услуги
     * @apiSuccess (200) {String} insurance_company_id Уникальный идентификатор страховой компании
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "id": "86f159ab-218a-442d-91d8-0064909d1e17",
     *       "name": "Первичный прием невролога"
     *       "insurance_company_id": "342bc567-f4c5-4f27-90d4-7094a76144cd"
     *     }
     * @apiError (400) {String} message Текст сообщения об ошибке
     * @apiError (404) {String} message Текст сообщения об ошибке
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "message": "Услуга не найден"
     *     }
     */
    public function show(ServerRequestInterface $request, ServiceRepositoryInterface $repository): JsonResponse
    {
        try {
            $id = $request->getAttribute('id');
            $serviceId = new ServiceId($id);

            $service = $repository->getOne($serviceId);

            return $this->createSuccessJsonResponse($service->toArray());
        } catch (ServiceNotFoundException $e) {
            return $this->createNotFoundJsonResponse(['message' => $e->getMessage()]);
        } catch (Exception $e) {
            return $this->createFailJsonResponse(['message' => $e->getMessage()]);
        }
    }
}
