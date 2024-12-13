<?php

declare(strict_types=1);

namespace App\Controller\Api\v1\InsuranceCompanies;

use App\Framework\Controller\AbstractController;
use App\Framework\Http\JsonResponse;
use App\Framework\Http\ServerRequestInterface;
use App\Model\InsuranceCompany\Exception\InsuranceCompanyNotFoundException;
use App\Model\InsuranceCompany\UseCase\Confirm\ConfirmInsuranceCompanyCommand;
use App\Model\InsuranceCompany\UseCase\Confirm\ConfirmInsuranceCompanyHandler;
use Exception;

class ConfirmInsuranceCompaniesController extends AbstractController
{
    /**
     * @api        {patch} /api/v1/insurance_companies/confirm/:token Подтверждение регистрации компании
     * @apiVersion 0.1.0
     * @apiName    Подтверждение регистрации компании
     * @apiGroup   Insurance company
     *
     * @apiParam {String} token Токен подтверждения регистрации компании
     *
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {}
     * @apiError (400) {String} message Текст сообщения об ошибке
     * @apiError (404) {String} message Текст сообщения об ошибке
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "message": "Страховая компания не найден"
     *     }
 */
    public function confirm(ServerRequestInterface $request, ConfirmInsuranceCompanyHandler $handler): JsonResponse
    {
        try {
            $emailConfirmToken = $request->getAttribute('token');

            $command = new ConfirmInsuranceCompanyCommand($emailConfirmToken);

            $handler->handle($command);

            return $this->createSuccessJsonResponse([]);
        } catch (InsuranceCompanyNotFoundException $e) {
            return $this->createNotFoundJsonResponse(['message' => $e->getMessage()]);
        } catch (Exception $e) {
            return $this->createFailJsonResponse(['message' => $e->getMessage()]);
        }
    }
}
