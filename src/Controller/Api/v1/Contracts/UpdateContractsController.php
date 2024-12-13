<?php

declare(strict_types=1);

namespace App\Controller\Api\v1\Contracts;

use App\Framework\Controller\AbstractController;
use App\Framework\Http\JsonResponse;
use App\Framework\Http\ServerRequestInterface;
use App\Model\Contract\Exception\Contract\ContractNotFoundException;
use App\Model\Contract\UseCase\Contract\Update\UpdateContractCommand;
use App\Model\Contract\UseCase\Contract\Update\UpdateContractForm;
use App\Model\Contract\UseCase\Contract\Update\UpdateContractHandler;
use App\Service\Hydrator\HydratorInterface;
use Exception;
use InvalidArgumentException;

class UpdateContractsController extends AbstractController
{
    public function __construct(private readonly HydratorInterface $hydrator)
    {
    }

    /**
     * @api        {post} /api/v1/contracts/:id Изменение договора
     * @apiVersion 1.0.0
     * @apiName    Изменение договора
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
     * @apiBody {String} number Номер договора
     * @apiBody {String} startDate Дата начала действия договора. В формате дд.мм.гггг
     * @apiBody {String} endDate Дата окончания действия договора. В формате дд.мм.гггг
     * @apiBody {Number} maxAmount Максимальная сумма по договору
     *
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {}
     * @apiError (400) {String} message Текст сообщения об ошибке
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 400 Bad Request
     *     {
     *       "message": "Договор с указанным номер уже существует"
     *     }
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 400 Bad Request
     *     {
     *       "message": "Максимальная сумма по договору должна быть больше нуля"
     *     }
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 400 Bad Request
     *     {
     *       "message": "Дата начала договора должна быть меньше даты окончания"
     *     }
     * @apiError (404) {String} message Текст сообщения об ошибке
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "message": "Договор не найден"
     *     }
    */
    public function update(ServerRequestInterface $request, UpdateContractHandler $handler): JsonResponse
    {
        try {
            $form = new UpdateContractForm();
            $form->handleRequest($request);

            if (!$form->isValid()) {
                throw new InvalidArgumentException($form->getErrorMessage());
            }

            $formData = array_merge($form->getValidData(), ['id' => $request->getAttribute('id')]);

            /* @var UpdateContractCommand $command */
            $command = $this->hydrator->hydrate(UpdateContractCommand::class, $formData);

            $handler->handle($command);

            return $this->createSuccessJsonResponse([]);
        } catch (ContractNotFoundException $e) {
            return $this->createNotFoundJsonResponse(['message' => $e->getMessage()]);
        } catch (Exception $e) {
            return $this->createFailJsonResponse(['message' => $e->getMessage()]);
        }
    }
}
