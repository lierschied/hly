<?php

namespace Core\Database;

use PDO;

class Orm
{
    private PDO $db;
    private QueryBuilder $builder;

    public function __construct()
    {
        $this->builder = new QueryBuilder();
    }

    /**
     * Select a database entry by given table name and id and returns an associative array
     * @param string|int $id
     * @param string $from
     * @param string $class
     * @param string $idColumn
     * @param string|null ...$select
     * @return mixed
     */
    public function get(string|int $id, string $from, string $class, string $idColumn = 'id', ?string ...$select): mixed
    {
        return $this->builder
            ->select(...$select)
            ->from($from)
            ->where($idColumn, $id)
            ->fetchObject($class);
    }

    public function findBy(string $column, string $where, string $from): array
    {
        return $this->builder
            ->select()
            ->from($from)
            ->where($column, $where)
            ->query();
    }
}