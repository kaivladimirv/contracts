<?php

declare(strict_types=1);

namespace App\Controller\Api\v1\InsuredPersons;

use App\Framework\Controller\AbstractController;
use App\Framework\Http\JsonResponse;
use App\Framework\Http\ServerRequestInterface;
use App\Model\Contract\Entity\ProvidedService\Id;
use App\Model\Contract\UseCase\ProvidedService\Registration\RegistrationProvidedServiceCommand;
use App\Model\Contract\UseCase\ProvidedService\Registration\RegistrationProvidedServiceForm;
use App\Model\Contract\UseCase\ProvidedService\Registration\RegistrationProvidedServiceHandler;
use App\Service\Hydrator\HydratorInterface;
use Exception;
use InvalidArgumentException;

class RegistrationProvidedServicesController extends AbstractController
{
    public function __construct(private readonly HydratorInterface $hydrator)
    {
    }

    /**
     * @api        {post} /api/v1/insured_persons/:insuredPersonId/provided_services/registration Регистрация оказанной услуги
     * @apiVersion 1.0.0
     * @apiName    Регистрация оказанной услуги
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
     * @apiBody {String} serviceId Идентификатор услуги
     * @apiBody {String} dateOfService Дата/время оказания услуги. В формате дд.мм.гггг чч:мм
     * @apiBody {Number} quantity Количество
     * @apiBody {Number} price Стоимость
     * @apiBody {Number} amount Сумма
     *
     * @apiSuccess (200) {String} id Уникальный идентификатор оказанной услуги
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "id": "cf7c645f-24fb-427f-9f5a-a79aa6b75867"
     *     }
     * @apiError (400) {String} message Текст сообщения об ошибке
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 400 Bad Request
     *     {
     *       "message": "Срок действия договора истёк"
     *     }
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 400 Bad Request
     *     {
     *       "message": "Услуга не покрывается договором"
     *     }
     */
    public function registration(
        ServerRequestInterface $request,
        RegistrationProvidedServiceHandler $handler
    ): JsonResponse {
        try {
            $form = new RegistrationProvidedServiceForm();
            $form->handleRequest($request);

            if (!$form->isValid()) {
                throw new InvalidArgumentException($form->getErrorMessage());
            }

            $id = Id::next();

            $formData = array_merge(
                $form->getValidData(),
                [
                    'id'              => $id->getValue(),
                    'insuredPersonId' => $request->getAttribute('insuredPersonId'),
                ]
            );

            /* @var RegistrationProvidedServiceCommand $command */
            $command = $this->hydrator->hydrate(RegistrationProvidedServiceCommand::class, $formData);

            $handler->handle($command);

            return $this->createSuccessJsonResponse(['id' => $id->getValue()]);
        } catch (Exception $e) {
            return $this->createFailJsonResponse(['message' => $e->getMessage()]);
        }
    }
}
