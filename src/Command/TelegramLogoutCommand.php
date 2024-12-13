<?php

declare(strict_types=1);

namespace App\Command;

use Override;
use App\Framework\Command\AbstractCommand;

/**
 * @psalm-api
 */
class TelegramLogoutCommand extends AbstractCommand
{
    public function __construct(private readonly string $sessionDirectoryPath)
    {
    }

    #[Override]
    protected function fillExpectedArguments(): void
    {
    }

    #[Override]
    protected function execute(): void
    {
        $this->deleteSession($this->sessionDirectoryPath);

        $this->console->info('OK');
    }

    private function deleteSession(string $sessionDirectoryPath): void
    {
        if (file_exists($sessionDirectoryPath)) {
            foreach (scandir($sessionDirectoryPath) as $f) {
                if ($f === '.' || $f === '..') {
                    continue;
                }

                unlink($sessionDirectoryPath . DIRECTORY_SEPARATOR . $f);
            }

            rmdir($sessionDirectoryPath);
        }
    }
}
