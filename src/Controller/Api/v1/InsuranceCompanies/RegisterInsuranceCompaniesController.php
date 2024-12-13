<?php

declare(strict_types=1);

namespace App\Controller\Api\v1\InsuranceCompanies;

use App\Framework\Controller\AbstractController;
use App\Framework\Http\JsonResponse;
use App\Framework\Http\ServerRequestInterface;
use App\Model\InsuranceCompany\Entity\InsuranceCompanyId;
use App\Model\InsuranceCompany\UseCase\Register\RegisterInsuranceCompanyCommand;
use App\Model\InsuranceCompany\UseCase\Register\RegisterInsuranceCompanyForm;
use App\Model\InsuranceCompany\UseCase\Register\RegisterInsuranceCompanyHandler;
use App\Service\Hydrator\HydratorInterface;
use Exception;
use InvalidArgumentException;

class RegisterInsuranceCompaniesController extends AbstractController
{
    public function __construct(private readonly HydratorInterface $hydrator)
    {
    }

    /**
     * @api        {post} /api/v1/insurance_companies/register Регистрация компании
     * @apiVersion 1.0.0
     * @apiName    Регистрация компании
     * @apiGroup   Insurance company
     *
     * @apiBody {String} name Название компании
     * @apiBody {String} email Адрес электронной почты
     * @apiBody {String} password Пароль
     *
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {}
     * @apiError (400) {String} message Текст сообщения об ошибке
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 400 Bad Request
     *     {
     *       "message": "Страховая компания с указанным названием уже зарегистрирована"
     *     }
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 400 Bad Request
     *     {
     *       "message": "Указанный электронный адрес уже используется"
     *     }
     */
    public function register(ServerRequestInterface $request, RegisterInsuranceCompanyHandler $handler): JsonResponse
    {
        try {
            $form = new RegisterInsuranceCompanyForm();
            $form->handleRequest($request);

            if (!$form->isValid()) {
                throw new InvalidArgumentException($form->getErrorMessage());
            }

            $id = InsuranceCompanyId::next();

            $formData = array_merge($form->getValidData(), ['id' => $id->getValue()]);

            /* @var RegisterInsuranceCompanyCommand $command */
            $command = $this->hydrator->hydrate(RegisterInsuranceCompanyCommand::class, $formData);

            $handler->handle($command);

            return $this->createSuccessJsonResponse([]);
        } catch (Exception $e) {
            return $this->createFailJsonResponse(['message' => $e->getMessage()]);
        }
    }
}
