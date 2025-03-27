<?php

declare(strict_types=1);

namespace App\Controller\Api\v1\Services;

use App\Framework\Controller\AbstractController;
use App\Framework\Http\JsonResponse;
use App\Framework\Http\ServerRequestInterface;
use App\Model\InsuranceCompany\Entity\InsuranceCompany;
use App\Model\Service\Entity\ServiceId;
use App\Model\Service\UseCase\Add\AddServiceCommand;
use App\Model\Service\UseCase\Add\AddServiceForm;
use App\Model\Service\UseCase\Add\AddServiceHandler;
use App\Service\Hydrator\HydratorInterface;
use Exception;
use InvalidArgumentException;

class AddServicesController extends AbstractController
{
    public function __construct(private readonly HydratorInterface $hydrator)
    {
    }

    /**
     * @api        {post} /api/v1/services/add Добавление услуги
     * @apiVersion 1.0.0
     * @apiName    Добавление услуги
     * @apiGroup   Service
     *
     * @apiHeader {String} Authorization Содержит строку формата Bearer {ACCESS_TOKEN}.
     *                                   Тип токена (Bearer) и сам токен доступа.
     *
     * @apiHeaderExample {String} Пример авторизации:
     *                            Authorization: Bearer aa028f85-0771-4d05-8354-b1273b88df77
     *
     * @apiBody {String} name Название услуги
     *
     * @apiSuccess (200) {String} id Идентификатор добавленной услуги
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "id": "5672162e-ee83-4ebc-988b-4c582ecd0f46"
     *     }
     * @apiError (400) {String} message Текст сообщения об ошибке
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 400 Bad Request
     *     {
     *       "message": "Услуга с указанным названием уже существует"
     *     }
     */
    public function add(ServerRequestInterface $request, AddServiceHandler $handler): JsonResponse
    {
        try {
            /* @var InsuranceCompany $insuranceCompany */
            $insuranceCompany = $request->getAttribute('insuranceCompany');

            $form = new AddServiceForm();
            $form->handleRequest($request);

            if (!$form->isValid()) {
                throw new InvalidArgumentException($form->getErrorMessage());
            }

            $id = ServiceId::next();

            $formData = array_merge(
                $form->getValidData(),
                [
                    'id'                 => $id->getValue(),
                    'insuranceCompanyId' => $insuranceCompany->getId()->getValue(),
                ]
            );

            $command = $this->hydrator->hydrate(AddServiceCommand::class, $formData);

            $handler->handle($command);

            return $this->createSuccessJsonResponse(['id' => $id->getValue()]);
        } catch (Exception $e) {
            return $this->createFailJsonResponse(['message' => $e->getMessage()]);
        }
    }
}
