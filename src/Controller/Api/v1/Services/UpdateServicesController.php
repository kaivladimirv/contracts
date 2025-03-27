<?php

declare(strict_types=1);

namespace App\Controller\Api\v1\Services;

use App\Framework\Controller\AbstractController;
use App\Framework\Http\JsonResponse;
use App\Framework\Http\ServerRequestInterface;
use App\Model\Service\Exception\ServiceNotFoundException;
use App\Model\Service\UseCase\Update\UpdateServiceCommand;
use App\Model\Service\UseCase\Update\UpdateServiceForm;
use App\Model\Service\UseCase\Update\UpdateServiceHandler;
use App\Service\Hydrator\HydratorInterface;
use Exception;
use InvalidArgumentException;

class UpdateServicesController extends AbstractController
{
    public function __construct(private readonly HydratorInterface $hydrator)
    {
    }

    /**
     * @api        {post} /api/v1/services/:id Изменение услуги
     * @apiVersion 1.0.0
     * @apiName    Изменение услуги
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
     * @apiBody {String} name Название услуги
     *
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {}
     * @apiError (400) {String} message Текст сообщения об ошибке
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 400 Bad Request
     *     {
     *       "message": "Услуга с указанным названием уже существует"
     *     }
     * @apiError (404) {String} message Текст сообщения об ошибке
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "message": "Услуга не найден"
     *     }
 */
    public function update(ServerRequestInterface $request, UpdateServiceHandler $handler): JsonResponse
    {
        try {
            $form = new UpdateServiceForm();
            $form->handleRequest($request);

            if (!$form->isValid()) {
                throw new InvalidArgumentException($form->getErrorMessage());
            }

            $formData = array_merge($form->getValidData(), ['id' => $request->getAttribute('id')]);

            $command = $this->hydrator->hydrate(UpdateServiceCommand::class, $formData);

            $handler->handle($command);

            return $this->createSuccessJsonResponse([]);
        } catch (ServiceNotFoundException $e) {
            return $this->createNotFoundJsonResponse(['message' => $e->getMessage()]);
        } catch (Exception $e) {
            return $this->createFailJsonResponse(['message' => $e->getMessage()]);
        }
    }
}
