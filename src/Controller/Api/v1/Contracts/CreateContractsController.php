<?php

declare(strict_types=1);

namespace App\Controller\Api\v1\Contracts;

use App\Framework\Controller\AbstractController;
use App\Framework\Http\JsonResponse;
use App\Framework\Http\ServerRequestInterface;
use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\UseCase\Contract\Create\CreateContractCommand;
use App\Model\Contract\UseCase\Contract\Create\CreateContractForm;
use App\Model\Contract\UseCase\Contract\Create\CreateContractHandler;
use App\Model\InsuranceCompany\Entity\InsuranceCompany;
use App\Service\Hydrator\HydratorInterface;
use Exception;
use InvalidArgumentException;

class CreateContractsController extends AbstractController
{
    public function __construct(private readonly HydratorInterface $hydrator)
    {
    }

    /**
     * @api        {post} /api/v1/contracts/create Создание нового договора
     * @apiVersion 1.0.0
     * @apiName    Создание нового договора
     * @apiGroup   Contract
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
     * @apiSuccess (200) {String} id Идентификатор созданного договора
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "id": "9dc5aa54-34c9-445c-b6ae-2992955d76a4"
     *     }
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
     */
    public function create(ServerRequestInterface $request, CreateContractHandler $handler): JsonResponse
    {
        try {
            /* @var InsuranceCompany $insuranceCompany */
            $insuranceCompany = $request->getAttribute('insuranceCompany');

            $form = new CreateContractForm();
            $form->handleRequest($request);

            if (!$form->isValid()) {
                throw new InvalidArgumentException($form->getErrorMessage());
            }

            $id = ContractId::next();

            $formData = array_merge(
                $form->getValidData(),
                [
                    'id'                 => $id->getValue(),
                    'insuranceCompanyId' => $insuranceCompany->getId()->getValue(),
                ]
            );

            $command = $this->hydrator->hydrate(CreateContractCommand::class, $formData);

            $handler->handle($command);

            return $this->createSuccessJsonResponse(['id' => $id->getValue()]);
        } catch (Exception $e) {
            return $this->createFailJsonResponse(['message' => $e->getMessage()]);
        }
    }
}
