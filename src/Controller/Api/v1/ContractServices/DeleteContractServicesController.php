<?php

declare(strict_types=1);

namespace App\Controller\Api\v1\ContractServices;

use App\Framework\Controller\AbstractController;
use App\Framework\Http\ResponseInterface;
use App\Framework\Http\ServerRequestInterface;
use App\Model\Contract\Exception\ContractService\ContractServiceNotFoundException;
use App\Model\Contract\UseCase\ContractService\Delete\DeleteContractServiceCommand;
use App\Model\Contract\UseCase\ContractService\Delete\DeleteContractServiceHandler;
use App\Service\Hydrator\HydratorInterface;
use Exception;

class DeleteContractServicesController extends AbstractController
{
    public function __construct(private readonly HydratorInterface $hydrator)
    {
    }

    /**
     * @api        {delete} /api/v1/contracts/:contractId/services/:serviceId Удаление услуги из договора
     * @apiVersion 1.0.0
     * @apiName    Удаление услуги из договора
     * @apiGroup   Contract service
     *
     * @apiParam {String} contractId Идентификатор договора
     * @apiParam {String} serviceId Идентификатор услуги
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
    public function delete(ServerRequestInterface $request, DeleteContractServiceHandler $handler): ResponseInterface
    {
        try {
            /* @var DeleteContractServiceCommand $command */
            $command = $this->hydrator->hydrate(
                DeleteContractServiceCommand::class,
                [
                    'contractId' => $request->getAttribute('contractId'),
                    'serviceId'  => $request->getAttribute('serviceId'),
                ]
            );

            $handler->handle($command);

            return $this->createSuccessResponseWithoutContent();
        } catch (ContractServiceNotFoundException) {
            return $this->createSuccessResponseWithoutContent();
        } catch (Exception $e) {
            return $this->createFailJsonResponse(['message' => $e->getMessage()]);
        }
    }
}
