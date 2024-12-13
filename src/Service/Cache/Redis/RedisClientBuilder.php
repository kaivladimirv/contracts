<?php

declare(strict_types=1);

namespace App\Service\Cache\Redis;

use App\Service\Cache\Exception\AuthenticationException;
use App\Service\Cache\Exception\ConnectionException;
use Redis;
use RedisException;

class RedisClientBuilder
{
    private string $host;
    private int $port;
    private int $dbIndex;
    private string $password;

    public function setHost(string $host): self
    {
        $this->host = $host;

        return $this;
    }

    public function setPort(int $port): self
    {
        $this->port = $port;

        return $this;
    }

    public function setDbIndex(int $dbIndex): self
    {
        $this->dbIndex = $dbIndex;

        return $this;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @throws AuthenticationException
     * @throws ConnectionException
     */
    public function build(): Redis
    {
        $redisClient = new Redis();

        try {
            $redisClient->connect($this->host, $this->port);

            if ($this->password and !$redisClient->auth($this->password)) {
                throw new AuthenticationException('Cache server connection authentication error');
            }

            $redisClient->select($this->dbIndex);
        } catch (RedisException) {
            throw new ConnectionException('Error connecting to the cache server');
        }

        return $redisClient;
    }
}
