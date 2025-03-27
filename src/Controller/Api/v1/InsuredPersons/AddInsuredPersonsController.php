<?php

declare(strict_types=1);

namespace App\Controller\Api\v1\InsuredPersons;

use App\Framework\Controller\AbstractController;
use App\Framework\Http\JsonResponse;
use App\Framework\Http\ServerRequestInterface;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\Model\Contract\UseCase\InsuredPerson\Add\AddInsuredPersonCommand;
use App\Model\Contract\UseCase\InsuredPerson\Add\AddInsuredPersonForm;
use App\Model\Contract\UseCase\InsuredPerson\Add\AddInsuredPersonHandler;
use App\Service\Hydrator\HydratorInterface;
use Exception;
use InvalidArgumentException;

class AddInsuredPersonsController extends AbstractController
{
    public function __construct(private readonly HydratorInterface $hydrator)
    {
    }

    /**
     * @api        {post} /api/v1/insured_persons/add Добавление застрахованного лица
     * @apiVersion 1.0.0
     * @apiName    Добавление застрахованного лица
     * @apiGroup   Insured person
     *
     * @apiHeader {String} Authorization Содержит строку формата Bearer {ACCESS_TOKEN}.
     *                                   Тип токена (Bearer) и сам токен доступа.
     *
     * @apiHeaderExample {String} Пример авторизации:
     *                            Authorization: Bearer aa028f85-0771-4d05-8354-b1273b88df77
     *
     * @apiBody {String} contractId Идентификатор договора
     * @apiBody {String} personId Идентификатор персоны
     * @apiBody {String} policyNumber Номер страхового полиса
     * @apiBody {Number=0,1} isAllowedToExceedLimit Разрешение на превышение лимита: 0 - не разрешено, 1 - разрешено
     *
     * @apiSuccess (200) {String} id Идентификатор добавленного застрахованного лица
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "id": "97776067-d2c2-4773-b47d-e926206a2fa6"
     *     }
     * @apiError (400) {String} message Текст сообщения об ошибке
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 400 Bad Request
     *     {
     *       "message": "Указанная персона уже добавлена в договор"
     *     }
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 400 Bad Request
     *     {
     *       "message": "Указанный номер полиса уже присвоен другому застрахованному лицу"
     *     }
     */
    public function add(ServerRequestInterface $request, AddInsuredPersonHandler $handler): JsonResponse
    {
        try {
            $form = new AddInsuredPersonForm();
            $form->handleRequest($request);

            if (!$form->isValid()) {
                throw new InvalidArgumentException($form->getErrorMessage());
            }

            $id = InsuredPersonId::next();

            $formData = array_merge($form->getValidData(), ['id' => $id->getValue()]);

            $command = $this->hydrator->hydrate(AddInsuredPersonCommand::class, $formData);

            $handler->handle($command);

            return $this->createSuccessJsonResponse(['id' => $id->getValue()]);
        } catch (Exception $e) {
            return $this->createFailJsonResponse(['message' => $e->getMessage()]);
        }
    }
}
