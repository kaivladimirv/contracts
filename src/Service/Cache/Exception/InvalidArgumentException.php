<?php

declare(strict_types=1);

namespace App\Service\Cache\Exception;

use Exception;

class InvalidArgumentException extends Exception implements CacheExceptionInterface
{
}
