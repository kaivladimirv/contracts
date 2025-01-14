<?php

declare(strict_types=1);

namespace App\Controller\Api\v1\InsuranceCompanies;

use App\Framework\Controller\AbstractController;
use App\Framework\Http\JsonResponse;
use App\Framework\Http\ServerRequestInterface;
use App\Model\InsuranceCompany\Entity\InsuranceCompany;
use App\Model\InsuranceCompany\Exception\InsuranceCompanyNotFoundException;
use App\Model\InsuranceCompany\Service\AccessTokenGenerator;
use App\Model\InsuranceCompany\UseCase\AccessToken\Save\SaveAccessTokenInsuranceCompanyCommand;
use App\Model\InsuranceCompany\UseCase\AccessToken\Save\SaveAccessTokenInsuranceCompanyHandler;
use DateInterval;
use DateTimeImmutable;
use Exception;

class AccessTokenInsuranceCompaniesController extends AbstractController
{
    /**
     * @api        {get} /api/v1/insurance_companies/token Получение токена доступа
     * @apiVersion 0.1.0
     * @apiName    Получение токена доступа
     * @apiGroup   Insurance company
     *
     * @apiHeader {String} Authorization Строка закодированная в base64, состоящая из ЛОГИН:ПАРОЛЬ (Подробнее:
     *            https://en.wikipedia.org/wiki/Basic_access_authentication).
     *            В качестве логина используется email компании.
     *
     * @apiHeaderExample {String} Пример авторизации:
     *                            Authorization:Basic base64-encoded-string
     *
     * @apiSuccess (200) {String} token_type  Тип токена
     * @apiSuccess (200) {String} access_token Токен доступа
     * @apiSuccess (200) {String} expires Дата/время истечения токена
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "token_type": "Bearer",
     *       "access_token": "aa028f85-0771-4d05-8354-b1273b88df77",
     *       "expires": "2021-12-02T11:39:14+06:00"
     *     }
     * @apiError (400) {String} message Текст сообщения об ошибке
     * @apiError (404) {String} message Текст сообщения об ошибке
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "message": "Страховая компания не найден"
     *     }
 */
    public function get(
        ServerRequestInterface $request,
        SaveAccessTokenInsuranceCompanyHandler $handler
    ): JsonResponse {
        try {
            /* @var InsuranceCompany $insuranceCompany */
            $insuranceCompany = $request->getAttribute('insuranceCompany');
            $accessToken = $insuranceCompany->getAccessToken();

            if (!$accessToken or $accessToken->isExpiredTo(new DateTimeImmutable())) {
                $accessToken = new AccessTokenGenerator(new DateInterval('P30D'))->generate();

                $command = new SaveAccessTokenInsuranceCompanyCommand(
                    $insuranceCompany->getId()->getValue(),
                    $accessToken->getToken(),
                    $accessToken->getExpires()->format('c'),
                );

                $handler->handle($command);
            }

            return $this->createSuccessJsonResponse(
                [
                    'token_type'   => 'Bearer',
                    'access_token' => $accessToken->getToken(),
                    'expires'      => $accessToken->getExpires()->format('c'),
                ]
            );
        } catch (InsuranceCompanyNotFoundException $e) {
            return $this->createNotFoundJsonResponse(['message' => $e->getMessage()]);
        } catch (Exception $e) {
            return $this->createFailJsonResponse(['message' => $e->getMessage()]);
        }
    }
}
