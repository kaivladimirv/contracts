<?php

declare(strict_types=1);

namespace App\Controller\Api\v1\InsuredPersons;

use App\Framework\Controller\AbstractController;
use App\Framework\Http\JsonResponse;
use App\Framework\Http\ServerRequestInterface;
use App\Model\Contract\Exception\InsuredPerson\InsuredPersonNotFoundException;
use App\Model\Contract\UseCase\InsuredPerson\Update\UpdateInsuredPersonCommand;
use App\Model\Contract\UseCase\InsuredPerson\Update\UpdateInsuredPersonForm;
use App\Model\Contract\UseCase\InsuredPerson\Update\UpdateInsuredPersonHandler;
use App\Service\Hydrator\HydratorInterface;
use Exception;
use InvalidArgumentException;

class UpdateInsuredPersonsController extends AbstractController
{
    public function __construct(private readonly HydratorInterface $hydrator)
    {
    }

    /**
     * @api        {post} /api/v1/insured_persons/:insuredPersonId Изменение данных по застрахованному лицу
     * @apiVersion 1.0.0
     * @apiName    Изменение данных по застрахованному лицу
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
     * @apiBody {String} policyNumber Номер страхового полиса
     * @apiBody {Number=0,1} isAllowedToExceedLimit Разрешение на превышение лимита: 0 - не разрешено, 1 - разрешено
     *
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {}
     * @apiError (400) {String} message Текст сообщения об ошибке
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 400 Bad Request
     *     {
     *       "message": "Указанный номер полиса уже присвоен другому застрахованному лицу"
     *     }
     * @apiError (404) {String} message Текст сообщения об ошибке
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "message": "Застрахованное лицо не найдено"
     *     }
     */
    public function update(ServerRequestInterface $request, UpdateInsuredPersonHandler $handler): JsonResponse
    {
        try {
            $form = new UpdateInsuredPersonForm();
            $form->handleRequest($request);

            if (!$form->isValid()) {
                throw new InvalidArgumentException($form->getErrorMessage());
            }

            $formData = array_merge($form->getValidData(), ['insuredPersonId' => $request->getAttribute('insuredPersonId')]);

            /* @var UpdateInsuredPersonCommand $command */
            $command = $this->hydrator->hydrate(UpdateInsuredPersonCommand::class, $formData);

            $handler->handle($command);

            return $this->createSuccessJsonResponse([]);
        } catch (InsuredPersonNotFoundException $e) {
            return $this->createNotFoundJsonResponse(['message' => $e->getMessage()]);
        } catch (Exception $e) {
            return $this->createFailJsonResponse(['message' => $e->getMessage()]);
        }
    }
}
