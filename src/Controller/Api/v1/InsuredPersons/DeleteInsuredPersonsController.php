<?php

declare(strict_types=1);

namespace App\Controller\Api\v1\InsuredPersons;

use App\Framework\Controller\AbstractController;
use App\Framework\Http\ResponseInterface;
use App\Framework\Http\ServerRequestInterface;
use App\Model\Contract\Exception\InsuredPerson\InsuredPersonNotFoundException;
use App\Model\Contract\UseCase\InsuredPerson\Delete\DeleteInsuredPersonCommand;
use App\Model\Contract\UseCase\InsuredPerson\Delete\DeleteInsuredPersonHandler;
use App\Service\Hydrator\HydratorInterface;
use Exception;

class DeleteInsuredPersonsController extends AbstractController
{
    public function __construct(private readonly HydratorInterface $hydrator)
    {
    }

    /**
     * @api        {delete} /api/v1/insured_persons/:insuredPersonId Удаление застрахованного лица
     * @apiVersion 1.0.0
     * @apiName    Удаление застрахованного лица
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
     * @apiSuccessExample {String} Success-Response:
     *     HTTP/1.1 204 No Content
     * @apiError (400) {String} message Текст сообщения об ошибке
     */
    public function delete(ServerRequestInterface $request, DeleteInsuredPersonHandler $handler): ResponseInterface
    {
        try {
            $command = $this->hydrator->hydrate(
                DeleteInsuredPersonCommand::class,
                [
                    'insuredPersonId' => $request->getAttribute('insuredPersonId'),
                ]
            );

            $handler->handle($command);

            return $this->createSuccessResponseWithoutContent();
        } catch (InsuredPersonNotFoundException) {
            return $this->createSuccessResponseWithoutContent();
        } catch (Exception $e) {
            return $this->createFailJsonResponse(['message' => $e->getMessage()]);
        }
    }
}
