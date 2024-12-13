<?php

declare(strict_types=1);

namespace App\Framework\Database;

use App\Framework\Database\Exception\QueryBuilderException;
use PDO;
use PDOStatement;

class QueryBuilder
{
    private const int QUERY_TYPE__SELECT           = 0;
    private const int QUERY_TYPE__INSERT           = 1;
    private const int QUERY_TYPE__UPDATE           = 2;
    private const int QUERY_TYPE__DELETE           = 3;
    private const int QUERY_TYPE__SUM              = 4;
    private const int QUERY_TYPE__UPDATE_OR_INSERT = 5;
    private const int QUERY_TYPE__COUNT              = 6;
    private ?int $currentQueryType = null;
    private string $tableName;
    private array $select     = [];
    private array $insertData = [];
    private array $updateData = [];
    private int $limit      = 0;
    private int $skip       = 0;
    private array $andWhere   = [];
    private array $orderBy    = [];
    private array $matching   = [];
    private array $join       = [];

    public function __construct(private readonly PDO $pdoConnection)
    {
    }

    public function select(array $fieldNames): self
    {
        $this->currentQueryType = self::QUERY_TYPE__SELECT;

        $this->select = $fieldNames;

        return $this;
    }

    public function table(string $tableName): self
    {
        $this->tableName = $tableName;

        return $this;
    }

    /**
     * @throws QueryBuilderException
     */
    public function limit(int $limit): self
    {
        if ($limit <= 0) {
            throw new QueryBuilderException('Параметр limit должен быть больше нуля');
        }

        $this->limit = $limit;

        return $this;
    }

    /**
     * @throws QueryBuilderException
     */
    public function skip(int $skip): self
    {
        if ($skip < 0) {
            throw new QueryBuilderException('Параметр skip должен быть больше или равен нулю');
        }

        $this->skip = $skip;

        return $this;
    }

    public function innerJoin(string $tableName, string $condition): self
    {
        $this->join[] = "inner join $tableName on $condition";

        return $this;
    }

    public function leftJoin(string $tableName, string $condition): self
    {
        $this->join[] = "left join $tableName on $condition";

        return $this;
    }

    public function andWhere(array $params): self
    {
        $this->andWhere = array_merge($this->andWhere, $params);

        return $this;
    }

    public function orderBy(array $fieldNames): self
    {
        $this->orderBy = $fieldNames;

        return $this;
    }

    public function insert(array $data): self
    {
        $this->currentQueryType = self::QUERY_TYPE__INSERT;

        $this->insertData = $data;

        return $this;
    }

    public function update(array $data): self
    {
        $this->currentQueryType = self::QUERY_TYPE__UPDATE;

        $this->updateData = $data;

        return $this;
    }

    public function delete(): self
    {
        $this->currentQueryType = self::QUERY_TYPE__DELETE;

        return $this;
    }

    public function updateOrInsert(array $updateData, array $insertData, array $matching): self
    {
        $this->currentQueryType = self::QUERY_TYPE__UPDATE_OR_INSERT;

        $this->updateData = $updateData;
        $this->insertData = $insertData;
        $this->matching = $matching;

        return $this;
    }

    /**
     * @throws QueryBuilderException
     */
    public function execute(): void
    {
        switch ($this->currentQueryType) {
            case self::QUERY_TYPE__INSERT:
                $this->executeInsert();
                $this->reset();
                break;

            case self::QUERY_TYPE__UPDATE:
                $this->executeUpdate();
                $this->reset();
                break;

            case self::QUERY_TYPE__DELETE:
                $this->executeDelete();
                $this->reset();
                break;

            case self::QUERY_TYPE__UPDATE_OR_INSERT:
                $this->executeUpdateOrInsert();
                $this->reset();
                break;

            default:
                $this->reset();
                throw new QueryBuilderException('Неизвестный тип запроса');
        }
    }

    /**
     * @throws QueryBuilderException
     */
    public function sum(string $fieldName): float|int
    {
        $this->currentQueryType = self::QUERY_TYPE__SUM;

        $this->select = [$fieldName];

        $data = $this->fetch();
        $value  = array_shift($data) ?? 0;

        return $this->convertToNumber($value);
    }

    private function convertToNumber(mixed $value): float|int
    {
        if (filter_var($value, FILTER_VALIDATE_INT) !== false) {
            return (int)$value;
        }

        return (float)$value;
    }

    /**
     * @throws QueryBuilderException
     */
    public function count(string $fieldName): int
    {
        $this->currentQueryType = self::QUERY_TYPE__COUNT;

        $this->select = [$fieldName];

        $data = $this->fetch();

        return ($data ? array_shift($data) : 0);
    }

    /**
     * @throws QueryBuilderException
     */
    public function fetch(): array
    {
        $data = $this->fetchAll();

        return ($data ? array_shift($data) : []);
    }

    /**
     * @throws QueryBuilderException
     */
    public function fetchAll(): array
    {
        $this->throwExceptionIfQueryIsNotFetch();

        $stmt = $this->executeSelect();

        $this->reset();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @throws QueryBuilderException
     */
    private function throwExceptionIfQueryIsNotFetch(): void
    {
        if (
            !in_array($this->currentQueryType, [
            self::QUERY_TYPE__SELECT,
            self::QUERY_TYPE__SUM,
            self::QUERY_TYPE__COUNT
            ])
        ) {
            throw new QueryBuilderException('Запрос не является запросам на выборку');
        }
    }

    /**
     * @throws QueryBuilderException
     */
    private function executeInsert(): void
    {
        $sql = $this->constructParameterizedInsertSql();

        $this->executeSql($sql);
    }

    /**
     * @throws QueryBuilderException
     */
    private function executeUpdate(): void
    {
        $sql = $this->constructParameterizedUpdateSql();

        $this->executeSql($sql);
    }

    /**
     * @throws QueryBuilderException
     */
    private function executeDelete(): void
    {
        $sql = $this->constructParameterizedDeleteSql();

        $this->executeSql($sql);
    }

    /**
     * @throws QueryBuilderException
     */
    private function executeUpdateOrInsert(): void
    {
        $sql = $this->constructParameterizedUpdateOrInsertSql();

        $this->executeSql($sql);
    }

    /**
     * @throws QueryBuilderException
     */
    private function executeSelect(): PDOStatement
    {
        $sql = $this->constructParameterizedSelectSql();

        return $this->executeSql($sql);
    }

    private function extractFieldNamesFrom(array $data): array
    {
        return array_keys($data);
    }

    private function constructParameterizedInsertSql(): string
    {
        $fieldNames = $this->extractFieldNamesFrom($this->insertData);
        $fieldCount = count($fieldNames);

        return 'INSERT INTO ' . $this->tableName . ' (' . implode(',', $fieldNames) . ') 
            VALUES (' . implode(',', array_fill(0, $fieldCount, '?')) . ');';
    }

    private function constructParameterizedUpdateSql(): string
    {
        $fieldNames = $this->extractFieldNamesFrom($this->updateData);
        $setFields = array_map(fn($fieldName) => "$fieldName = ?", $fieldNames);

        return 'UPDATE ' . $this->tableName .
            ' SET ' . implode(',', $setFields) .
            ' WHERE ' . $this->constructAndWhere() . ';';
    }

    private function constructParameterizedDeleteSql(): string
    {
        return 'DELETE FROM ' . $this->tableName .
            ' WHERE ' . $this->constructAndWhere() . ';';
    }

    private function constructParameterizedUpdateOrInsertSql(): string
    {
        $insertFieldNames = $this->extractFieldNamesFrom($this->insertData);
        $insertFieldCount = count($insertFieldNames);

        $fieldNames = $this->extractFieldNamesFrom($this->updateData);
        $setFields = array_map(fn($fieldName) => "$fieldName = ?", $fieldNames);

        $matchingFieldNames = $this->matching;

        return 'INSERT INTO ' . $this->tableName . ' (' . implode(',', $insertFieldNames) . ') ' .
            'VALUES (' . implode(',', array_fill(0, $insertFieldCount, '?')) . ') ' .
            'ON CONFLICT (' . implode(',', $matchingFieldNames) . ') ' .
            'DO UPDATE SET ' . implode(',', $setFields);
    }

    private function constructParameterizedSelectSql(): string
    {
        $sql = [];
        $sql[] = $this->constructSelect();
        $sql[] = 'FROM ' . $this->tableName;

        if (!empty($this->join)) {
            array_push($sql, ...$this->join);
        }

        if (!empty($this->andWhere)) {
            $sql[] = 'WHERE ' . $this->constructAndWhere();
        }

        if (!empty($this->orderBy)) {
            $sql[] = 'ORDER BY ' . implode(',', $this->orderBy);
        }

        if ($this->limit) {
            $sql[] = 'LIMIT ' . $this->limit;
            if ($this->skip) {
                $sql[] = 'OFFSET ' . $this->skip;
            }
        }

        return implode(' ', $sql) . ';';
    }

    private function constructSelect(): string
    {
        return match ($this->currentQueryType) {
            self::QUERY_TYPE__SUM => 'SELECT sum(' . $this->select[0] . ')',
            self::QUERY_TYPE__COUNT => 'SELECT count(' . $this->select[0] . ')',
            default => 'SELECT ' . implode(',', $this->select),
        };
    }

    private function constructAndWhere(): string
    {
        $where = [];
        foreach ($this->andWhere as $fieldName => $condition) {
            $where[] = is_numeric($fieldName) ? $condition : "$fieldName = ?";
        }

        return implode(' AND ', $where);
    }

    /**
     * @throws QueryBuilderException
     */
    private function executeSql(string $sql): PDOStatement
    {
        $stmt = $this->prepareQuery($sql);

        $this->bindValues($stmt);

        return $this->executeStatement($stmt);
    }

    /**
     * @throws QueryBuilderException
     */
    private function prepareQuery(string $sql): PDOStatement
    {
        $stmt = $this->pdoConnection->prepare($sql);

        if ($stmt !== false) {
            return $stmt;
        }

        $errorMessage = $this->pdoConnection->errorInfo()[2];

        throw new QueryBuilderException($errorMessage);
    }

    private function bindValues(PDOStatement $stmt): void
    {
        $index = 1;

        if (
            in_array($this->currentQueryType, [
            self::QUERY_TYPE__INSERT,
            self::QUERY_TYPE__UPDATE_OR_INSERT,
            ])
        ) {
            foreach ($this->insertData as $value) {
                $stmt->bindValue($index, $value);
                $index++;
            }
        }

        if (
            in_array($this->currentQueryType, [
            self::QUERY_TYPE__UPDATE,
            self::QUERY_TYPE__UPDATE_OR_INSERT,
            ])
        ) {
            foreach ($this->updateData as $value) {
                $stmt->bindValue($index, $value);
                $index++;
            }
        }

        foreach ($this->andWhere as $fieldName => $value) {
            if (!is_numeric($fieldName)) {
                $stmt->bindValue($index, $value);
                $index++;
            }
        }
    }

    /**
     * @throws QueryBuilderException
     */
    private function executeStatement(PDOStatement $stmt): PDOStatement
    {
        if ($stmt->execute() !== false) {
            return $stmt;
        }

        $errorMessage = $stmt->errorInfo()[2];

        throw new QueryBuilderException($errorMessage);
    }

    private function reset(): void
    {
        $this->currentQueryType = null;
        $this->tableName = '';
        $this->select = [];
        $this->insertData = [];
        $this->updateData = [];
        $this->andWhere = [];
        $this->orderBy = [];
        $this->matching = [];
        $this->join = [];
    }
}
