<?php

declare(strict_types=1);

namespace App\Controller\Api\v1\Persons;

use App\Framework\Controller\AbstractController;
use App\Framework\Http\JsonResponse;
use App\Framework\Http\ServerRequestInterface;
use App\Model\Person\Exception\PersonNotFoundException;
use App\Model\Person\UseCase\Update\UpdatePersonCommand;
use App\Model\Person\UseCase\Update\UpdatePersonForm;
use App\Model\Person\UseCase\Update\UpdatePersonHandler;
use App\Service\Hydrator\HydratorInterface;
use Exception;
use InvalidArgumentException;

class UpdatePersonsController extends AbstractController
{
    public function __construct(private readonly HydratorInterface $hydrator)
    {
    }

    /**
     * @api        {post} /api/v1/persons/:id Изменение персоны
     * @apiVersion 1.0.0
     * @apiName    Изменение персоны
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
     * @apiBody {String} lastName Фамилия
     * @apiBody {String} firstName Имя
     * @apiBody {String} middleName Отчество
     * @apiBody {String} email Электронный адрес
     * @apiBody {Number} [phoneNumber] Номер мобильного телефона
     * @apiBody {Number=0,1} [notifierType] 0 - уведомлять по email, 1 - уведомлять по telegram
     *
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {}
     * @apiError (400) {String} message Текст сообщения об ошибке
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 400 Bad Request
     *     {
     *       "message": "Персона с указанным именем уже существует"
     *     }
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 400 Bad Request
     *     {
     *       "message": "Персона с указанным электронным адресом уже существует"
     *     }
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 400 Bad Request
     *     {
     *       "message": "Персона с указанным номером телефона уже существует"
     *     }
     * @apiError (404) {String} message Текст сообщения об ошибке
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "message": "Персона не найден"
     *     }
 */
    public function update(ServerRequestInterface $request, UpdatePersonHandler $handler): JsonResponse
    {
        try {
            $form = new UpdatePersonForm();
            $form->handleRequest($request);

            if (!$form->isValid()) {
                throw new InvalidArgumentException($form->getErrorMessage());
            }

            $formData = array_merge($form->getValidData(), ['id' => $request->getAttribute('id')]);

            $command = $this->hydrator->hydrate(UpdatePersonCommand::class, $formData);

            $handler->handle($command);

            return $this->createSuccessJsonResponse([]);
        } catch (PersonNotFoundException $e) {
            return $this->createNotFoundJsonResponse(['message' => $e->getMessage()]);
        } catch (Exception $e) {
            return $this->createFailJsonResponse(['message' => $e->getMessage()]);
        }
    }
}
