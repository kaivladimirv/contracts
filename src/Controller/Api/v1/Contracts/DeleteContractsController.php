<?php

declare(strict_types=1);

namespace App\Controller\Api\v1\Contracts;

use App\Framework\Controller\AbstractController;
use App\Framework\Http\ResponseInterface;
use App\Framework\Http\ServerRequestInterface;
use App\Model\Contract\Exception\Contract\ContractNotFoundException;
use App\Model\Contract\UseCase\Contract\Delete\DeleteContractCommand;
use App\Model\Contract\UseCase\Contract\Delete\DeleteContractHandler;
use Exception;

class DeleteContractsController extends AbstractController
{
    /**
     * @api        {delete} /api/v1/contracts/:id Удаление договора
     * @apiVersion 1.0.0
     * @apiName    Удаление договора
     * @apiGroup   Contract
     *
     * @apiParam {String} id Уникальный идентификатор договора
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
    public function delete(ServerRequestInterface $request, DeleteContractHandler $handler): ResponseInterface
    {
        try {
            $command = new DeleteContractCommand($request->getAttribute('id'));

            $handler->handle($command);

            return $this->createSuccessResponseWithoutContent();
        } catch (ContractNotFoundException) {
            return $this->createSuccessResponseWithoutContent();
        } catch (Exception $e) {
            return $this->createFailJsonResponse(['message' => $e->getMessage()]);
        }
    }
}
