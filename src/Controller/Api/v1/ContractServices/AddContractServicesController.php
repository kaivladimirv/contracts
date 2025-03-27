<?php

declare(strict_types=1);

namespace App\Controller\Api\v1\ContractServices;

use App\Framework\Controller\AbstractController;
use App\Framework\Http\JsonResponse;
use App\Framework\Http\ServerRequestInterface;
use App\Model\Contract\Entity\ContractService\ContractServiceId;
use App\Model\Contract\UseCase\ContractService\Add\AddContractServiceCommand;
use App\Model\Contract\UseCase\ContractService\Add\AddContractServiceForm;
use App\Model\Contract\UseCase\ContractService\Add\AddContractServiceHandler;
use App\Service\Hydrator\HydratorInterface;
use Exception;
use InvalidArgumentException;

class AddContractServicesController extends AbstractController
{
    public function __construct(private readonly HydratorInterface $hydrator)
    {
    }

    /**
     * @api        {post} /api/v1/contracts/:contractId/services/add Добавление услуги в договор
     * @apiVersion 1.0.0
     * @apiName    Добавление услуги в договор
     * @apiGroup   Contract service
     *
     * @apiParam {String} contractId Идентификатор договора
     *
     * @apiHeader {String} Authorization Содержит строку формата Bearer {ACCESS_TOKEN}.
     *                                   Тип токена (Bearer) и сам токен доступа.
     *
     * @apiHeaderExample {String} Пример авторизации:
     *                            Authorization: Bearer aa028f85-0771-4d05-8354-b1273b88df77
     *
     * @apiBody {String} serviceId Идентификатор услуги
     * @apiBody {Number} limitType Тип лимита
     * @apiBody {Number} limitValue Значение лимита
     *
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *     }
     * @apiError (400) {String} message Текст сообщения об ошибке
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 400 Bad Request
     *     {
     *       "message": "Услуга уже добавлена в договор"
     *     }
     */
    public function add(ServerRequestInterface $request, AddContractServiceHandler $handler): JsonResponse
    {
        try {
            $form = new AddContractServiceForm();
            $form->handleRequest($request);

            if (!$form->isValid()) {
                throw new InvalidArgumentException($form->getErrorMessage());
            }

            $formData = array_merge(
                $form->getValidData(),
                [
                    'id'         => ContractServiceId::next()->getValue(),
                    'contractId' => $request->getAttribute('contractId'),
                ]
            );

            $command = $this->hydrator->hydrate(AddContractServiceCommand::class, $formData);

            $handler->handle($command);

            return $this->createSuccessJsonResponse([]);
        } catch (Exception $e) {
            return $this->createFailJsonResponse(['message' => $e->getMessage()]);
        }
    }
}
