<?php

declare(strict_types=1);

namespace App\Controller\Api\v1\ContractServices;

use App\Framework\Controller\AbstractController;
use App\Framework\Http\JsonResponse;
use App\Framework\Http\ServerRequestInterface;
use App\Model\Contract\Exception\ContractService\ContractServiceNotFoundException;
use App\Model\Contract\UseCase\ContractService\Update\UpdateContractServiceCommand;
use App\Model\Contract\UseCase\ContractService\Update\UpdateContractServiceForm;
use App\Model\Contract\UseCase\ContractService\Update\UpdateContractServiceHandler;
use App\Service\Hydrator\HydratorInterface;
use Exception;
use InvalidArgumentException;

class UpdateContractServicesController extends AbstractController
{
    public function __construct(private readonly HydratorInterface $hydrator)
    {
    }

    /**
     * @api        {post} /api/v1/contracts/:contractId/services/:serviceId Изменение услуги входящей в договор
     * @apiVersion 1.0.0
     * @apiName    Изменение услуги входящей в договор
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
     * @apiBody {Number} limitValue Значение лимита
     *
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {}
     * @apiError (400) {String} message Текст сообщения об ошибке
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 400 Bad Request
     *     {
     *       "message": "Значение лимита должно быть больше нуля"
     *     }
     * @apiError (404) {String} message Текст сообщения об ошибке
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Bad Request
     *     {
     *       "message": "Услуга не найдена в договоре"
     *     }
     */
    public function update(ServerRequestInterface $request, UpdateContractServiceHandler $handler): JsonResponse
    {
        try {
            $form = new UpdateContractServiceForm();
            $form->handleRequest($request);

            if (!$form->isValid()) {
                throw new InvalidArgumentException($form->getErrorMessage());
            }

            $formDate = array_merge(
                $form->getValidData(),
                [
                    'contractId' => $request->getAttribute('contractId'),
                    'serviceId'  => $request->getAttribute('serviceId'),
                ]
            );

            /* @var UpdateContractServiceCommand $command */
            $command = $this->hydrator->hydrate(UpdateContractServiceCommand::class, $formDate);

            $handler->handle($command);

            return $this->createSuccessJsonResponse([]);
        } catch (ContractServiceNotFoundException $e) {
            return $this->createNotFoundJsonResponse(['message' => $e->getMessage()]);
        } catch (Exception $e) {
            return $this->createFailJsonResponse(['message' => $e->getMessage()]);
        }
    }
}
