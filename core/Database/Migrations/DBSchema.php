<?php

namespace Core\Database\Migrations;

use Core\Database\QueryBuilder;
use PDO;

class DBSchema
{
    public function __construct(private QueryBuilder $queryBuilder = new QueryBuilder())
    {
    }

    public function getTableSchema(string $table): \Core\Database\Collection
    {
        return $this->queryBuilder
            ->executeSql("SHOW CREATE TABLE `$table`")
            ->getResults();
    }
}