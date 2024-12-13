<?php

declare(strict_types=1);

namespace App\Controller\Api\v1\Persons;

use App\Framework\Controller\AbstractController;
use App\Framework\Http\ResponseInterface;
use App\Framework\Http\ServerRequestInterface;
use App\Model\Person\Exception\PersonNotFoundException;
use App\Model\Person\UseCase\Delete\DeletePersonCommand;
use App\Model\Person\UseCase\Delete\DeletePersonHandler;
use Exception;

class DeletePersonsController extends AbstractController
{
    /**
     * @api        {delete} /api/v1/persons/:id Удаление персоны
     * @apiVersion 1.0.0
     * @apiName    Удаление персоны
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
     * @apiSuccessExample {String} Success-Response:
     *     HTTP/1.1 204 No Content
     * @apiError (400) {String} message Текст сообщения об ошибке
     */
    public function delete(ServerRequestInterface $request, DeletePersonHandler $handler): ResponseInterface
    {
        try {
            $command = new DeletePersonCommand($request->getAttribute('id'));

            $handler->handle($command);

            return $this->createSuccessResponseWithoutContent();
        } catch (PersonNotFoundException) {
            return $this->createSuccessResponseWithoutContent();
        } catch (Exception $e) {
            return $this->createFailJsonResponse(['message' => $e->getMessage()]);
        }
    }
}
