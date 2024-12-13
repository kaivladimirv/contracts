<?php

declare(strict_types=1);

namespace App\Command;

use Override;
use App\Framework\Command\AbstractCommand;
use PDO;
use PDOException;

/**
 * @psalm-api
 */
class DbCreateCommand extends AbstractCommand
{
    #[Override]
    protected function fillExpectedArguments(): void
    {
    }

    #[Override]
    protected function execute(): void
    {
        $dbHost = getenv('DB_HOST');
        $dbPort = getenv('DB_PORT');
        $dbUser = getenv('DB_USERNAME');
        $dbPassword = getenv('DB_PASSWORD');
        $dbName = getenv('DB_NAME');

        try {
            $pdo = new PDO("pgsql:host=$dbHost;port=$dbPort;dbname=postgres", $dbUser, $dbPassword);

            $select = $pdo->prepare('SELECT 1 FROM pg_database WHERE datname = :dbName;');
            $select->execute(['dbName' => $dbName]);

            if ($select->rowCount() === 0) {
                $pdo->exec("CREATE DATABASE $dbName;");

                $this->console->info("База данных '$dbName' успешно создана.");
            } else {
                $this->console->info("База данных '$dbName' уже существует.");
            }
        } catch (PDOException $exception) {
            $this->console->error('Failed to create database. Error: ' . $exception->getMessage());
        }
    }
}
