<?php

declare(strict_types=1);

namespace App\Controller\Api\v1\Persons;

use App\Framework\Controller\AbstractController;
use App\Framework\Http\JsonResponse;
use App\Framework\Http\ServerRequestInterface;
use App\Model\InsuranceCompany\Entity\InsuranceCompany;
use App\Model\Person\Entity\PersonId;
use App\Model\Person\UseCase\Add\AddPersonCommand;
use App\Model\Person\UseCase\Add\AddPersonForm;
use App\Model\Person\UseCase\Add\AddPersonHandler;
use App\Service\Hydrator\HydratorInterface;
use Exception;
use InvalidArgumentException;

class AddPersonsController extends AbstractController
{
    public function __construct(private readonly HydratorInterface $hydrator)
    {
    }

    /**
     * @api        {post} /api/v1/persons/add Добавление персоны
     * @apiVersion 1.0.0
     * @apiName    Добавление персоны
     * @apiGroup   Person
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
     * @apiSuccess (200) {String} id Идентификатор добавленной персоны
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "id": "3112762e-ee83-4ebc-988b-4c582ecd0f46"
     *     }
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
     */
    public function add(ServerRequestInterface $request, AddPersonHandler $handler): JsonResponse
    {
        try {
            /* @var InsuranceCompany $insuranceCompany */
            $insuranceCompany = $request->getAttribute('insuranceCompany');

            $form = new AddPersonForm();
            $form->handleRequest($request);

            if (!$form->isValid()) {
                throw new InvalidArgumentException($form->getErrorMessage());
            }

            $id = PersonId::next();

            $formData = array_merge(
                $form->getValidData(),
                [
                    'id'                 => $id->getValue(),
                    'insuranceCompanyId' => $insuranceCompany->getId()->getValue(),
                ]
            );

            $command = $this->hydrator->hydrate(AddPersonCommand::class, $formData);

            $handler->handle($command);

            return $this->createSuccessJsonResponse(['id' => $id->getValue()]);
        } catch (Exception $e) {
            return $this->createFailJsonResponse(['message' => $e->getMessage()]);
        }
    }
}
