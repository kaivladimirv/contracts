<?php

declare(strict_types=1);

namespace App\Controller\Api\v1\Services;

use App\Framework\Controller\AbstractController;
use App\Framework\Http\ResponseInterface;
use App\Framework\Http\ServerRequestInterface;
use App\Model\Service\Exception\ServiceNotFoundException;
use App\Model\Service\UseCase\Delete\DeleteServiceCommand;
use App\Model\Service\UseCase\Delete\DeleteServiceHandler;
use Exception;

class DeleteServicesController extends AbstractController
{
    /**
     * @api        {delete} /api/v1/services/:id Удаление услуги
     * @apiVersion 1.0.0
     * @apiName    Удаление услуги
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
     * @apiSuccessExample {String} Success-Response:
     *     HTTP/1.1 204 No Content
     * @apiError (400) {String} message Текст сообщения об ошибке
     */
    public function delete(ServerRequestInterface $request, DeleteServiceHandler $handler): ResponseInterface
    {
        try {
            $command = new DeleteServiceCommand($request->getAttribute('id'));

            $handler->handle($command);

            return $this->createSuccessResponseWithoutContent();
        } catch (ServiceNotFoundException) {
            return $this->createSuccessResponseWithoutContent();
        } catch (Exception $e) {
            return $this->createFailJsonResponse(['message' => $e->getMessage()]);
        }
    }
}
