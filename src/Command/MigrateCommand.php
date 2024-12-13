<?php

declare(strict_types=1);

namespace App\Command;

use Override;
use App\Framework\Command\AbstractCommand;
use PDO;
use PDOException;
use UnexpectedValueException;

/**
 * @psalm-api
 */
class MigrateCommand extends AbstractCommand
{
    public function __construct(private readonly PDO $pdoConnection)
    {
    }

    #[Override]
    protected function fillExpectedArguments(): void
    {
    }

    #[Override]
    protected function execute(): void
    {
        try {
            $migration = $this->readMigration(getenv('PATH_TO_MIGRATION_FILE'));

            $this->pdoConnection->exec($migration);

            $this->console->info('Миграция выполнена успешна.');
        } catch (PDOException $exception) {
            $this->console->error('Migration failed. Error: ' . $exception->getMessage());
        }
    }

    private function readMigration(string $path): string
    {
        $data = file_get_contents($path);

        if ($data === false) {
            throw new UnexpectedValueException("Файл $path не найден");
        }

        return $data;
    }
}
