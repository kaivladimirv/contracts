<?php

declare(strict_types=1);

namespace App\Framework\Form;

use App\Framework\Http\ServerRequestInterface;
use App\Framework\Validator\Validator;
use InvalidArgumentException;
use UnexpectedValueException;

abstract class AbstractForm
{
    private bool $isValid       = false;
    private string $errorMessage  = '';
    private array $validData     = [];
    protected string $requestMethod = 'POST';

    final public function handleRequest(ServerRequestInterface $request): void
    {
        foreach ($this->getRules() as $fieldName => $rules) {
            $value = $this->getParamValueFromRequest($fieldName, $request);

            if (!$this->isValid = Validator::validate($value, $rules)) {
                $this->errorMessage = sprintf(Validator::getErrorMessage(), $fieldName);

                return;
            }

            $this->validData[$fieldName] = $value;
        }

        $this->isValid = true;
    }

    private function getParamValueFromRequest(string $paramName, ServerRequestInterface $request): array|string
    {
        $this->throwExceptionIfUnknownRequestMethod();

        return ($this->requestMethod === 'POST' ? $request->getPostParam($paramName) : $request->getQueryParam($paramName));
    }

    abstract protected function getRules(): array;

    final public function isValid(): bool
    {
        return $this->isValid;
    }

    final public function throwExceptionIfIsNotValid(): void
    {
        if (!$this->isValid()) {
            throw new InvalidArgumentException($this->getErrorMessage());
        }
    }

    final public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    final public function getValidData(): array
    {
        return $this->validData;
    }

    private function throwExceptionIfUnknownRequestMethod(): void
    {
        if (
            !in_array($this->requestMethod, [
            'GET',
            'POST',
            ])
        ) {
            throw new UnexpectedValueException('Неизвестный метод запроса');
        }
    }
}
