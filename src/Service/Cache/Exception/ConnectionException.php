<?php

declare(strict_types=1);

namespace App\Service\Cache\Exception;

use Exception;

class ConnectionException extends Exception implements CacheExceptionInterface
{
}
