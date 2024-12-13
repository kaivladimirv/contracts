<?php

declare(strict_types=1);

namespace App\Service\LoggerActivity;

use Override;
use DomainException;
use InvalidArgumentException;

/**
 * @psalm-api
 */
class FileLoggerActivity extends AbstractLoggerActivity
{
    private const string SEPARATOR = "\t";

    private readonly string $pathToLogDirectory;

    public function __construct(ActorId $actorId, string $pathToLogDirectory)
    {
        $this->actorId = $actorId;

        if (!$pathToLogDirectory) {
            throw new InvalidArgumentException('Не указан путь к директории с логами');
        }

        if (!file_exists($pathToLogDirectory)) {
            throw new InvalidArgumentException("Директория $pathToLogDirectory не найдена");
        }

        if (!str_ends_with($pathToLogDirectory, '/')) {
            $pathToLogDirectory = $pathToLogDirectory . '/';
        }

        $this->pathToLogDirectory = $pathToLogDirectory;
    }

    #[Override]
    public function log(): void
    {
        parent::log();

        $fileName = $this->generateFileName();

        if (!file_put_contents($fileName, $this->buildLine() . PHP_EOL, FILE_APPEND)) {
            throw new DomainException('Не удалось сохранить лог');
        }
    }

    private function generateFileName(): string
    {
        return $this->pathToLogDirectory . date('Y-m-d');
    }

    private function buildLine(): string
    {
        $data = [
            $this->dateTime->format('Y-m-d H:i:s'),
            $this->logType,
            $this->actorId->getValue(),
            json_encode($this->data),
        ];

        return implode(self::SEPARATOR, $data);
    }
}
