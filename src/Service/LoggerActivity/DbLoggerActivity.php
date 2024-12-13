<?php

declare(strict_types=1);

namespace App\Service\LoggerActivity;

use Override;
use App\Framework\Database\Exception\QueryBuilderException;
use App\Framework\Database\QueryBuilder;

class DbLoggerActivity extends AbstractLoggerActivity
{
    private const string TABLE_NAME = 'log_activity';

    public function __construct(ActorId $actorId, private readonly QueryBuilder $queryBuilder)
    {
        $this->actorId = $actorId;
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function log(): void
    {
        parent::log();

        $data = [
            'datetime' => $this->dateTime->format('c'),
            'log_type' => $this->logType,
            'actor_id' => $this->actorId->getValue(),
            'data'     => json_encode($this->data),
        ];

        $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->insert($data)
            ->execute();
    }
}
