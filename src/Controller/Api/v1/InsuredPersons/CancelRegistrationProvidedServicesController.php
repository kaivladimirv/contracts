<?php

declare(strict_types=1);

namespace App\Controller\Api\v1\InsuredPersons;

use App\Framework\Controller\AbstractController;
use App\Framework\Http\JsonResponse;
use App\Framework\Http\ServerRequestInterface;
use App\Model\Contract\UseCase\ProvidedService\CancelRegistration\CancelRegistrationProvidedServiceCommand;
use App\Model\Contract\UseCase\ProvidedService\CancelRegistration\CancelRegistrationProvidedServiceHandler;
use App\Service\Hydrator\HydratorInterface;
use DateTimeImmutable;
use Exception;

class CancelRegistrationProvidedServicesController extends AbstractController
{
    public function __construct(private readonly HydratorInterface $hydrator)
    {
    }

    /**
     * @api        {post} /api/v1/insured_persons/:insuredPersonId/provided_services/:id/cancel_registration Отмена регистрации оказанной услуги
     * @apiVersion 1.0.0
     * @apiName    Отмена регистрации оказанной услуги
     * @apiGroup   Insured person
     *
     * @apiParam {String} insuredPersonId Идентификатор застрахованного лица
     * @apiParam {String} id Идентификатор оказанной услуги
     *
     * @apiHeader {String} Authorization Содержит строку формата Bearer {ACCESS_TOKEN}.
     *                                   Тип токена (Bearer) и сам токен доступа.
     *
     * @apiHeaderExample {String} Пример авторизации:
     *                            Authorization: Bearer aa028f85-0771-4d05-8354-b1273b88df77
     *
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *     }
     * @apiError (400) {String} message Текст сообщения об ошибке
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 400 Bad Request
     *     {
     *       "message": "Запись не найдена"
     *     }
     */
    public function cancelRegistration(
        ServerRequestInterface $request,
        CancelRegistrationProvidedServiceHandler $handler
    ): JsonResponse {
        try {
            /* @var CancelRegistrationProvidedServiceCommand $command */
            $command = $this->hydrator->hydrate(
                CancelRegistrationProvidedServiceCommand::class,
                [
                    'id'   => $request->getAttribute('id'),
                    'date' => new DateTimeImmutable(),
                ]
            );

            $handler->handle($command);

            return $this->createSuccessJsonResponse([]);
        } catch (Exception $e) {
            return $this->createFailJsonResponse(['message' => $e->getMessage()]);
        }
    }
}
