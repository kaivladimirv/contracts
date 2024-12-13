<?php

declare(strict_types=1);

namespace App\Command;

use Override;
use App\Framework\Command\AbstractCommand;
use App\Service\Cache\CacheItemPoolInterface;
use Exception;

/**
 * @psalm-api
 */
class ClearCacheCommand extends AbstractCommand
{
    public function __construct(private readonly CacheItemPoolInterface $cache)
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
            $this->cache->clear();

            $this->console->info('Очистка кэша успешна выполнена.');
        } catch (Exception $exception) {
            $this->console->error('Не удалось очистить кэш. Ошибка: ' . $exception->getMessage());
        }
    }
}
