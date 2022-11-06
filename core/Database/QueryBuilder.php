<?php

namespace Core\Database;

use PDO;
use PDOStatement;

class QueryBuilder
{
    private PDO $dbc;

    private array $fields = [];
    private array $from = [];
    private array $where = [
        'columns' => [],
        'values' => [],
    ];
    private PDOStatement $statement;
    private ?PDOStatement $result = null;

    public function __construct()
    {
        $this->dbc = Database::getInstance()->connection;
    }

    public function select(string ...$select): static
    {
        $this->fields = $select;
        return $this;
    }

    public function from(string $from, ?string $as = null): static
    {
        $this->from[] = $as === null ? $from : "$from AS $as";
        return $this;
    }

    public function where(string $column, string $value): static
    {
        $this->where['columns'][] = "$column = :$column";
        $this->where['values'][$column] = $value;
        return $this;
    }

    private function getFields(): string
    {
        $fields = implode(', ', $this->fields);
        return $fields ?: '*';
    }

    private function getFrom(): string
    {
        $from = implode(', ', $this->from);
        return $from ?: '';
    }

    public function __toString(): string
    {
        $where = $this->where['columns'] === [] ? '' : ' WHERE ' . implode(' AND ', $this->where['columns']);
        $fields = $this->getFields();
        $from = $this->getFrom();

        return "SELECT $fields FROM $from" . $where;
    }

    private function createStatement(): void
    {
        $this->statement = $this->dbc->prepare($this);
        $this->statement->execute($this->where['values']);
    }

    public function query()
    {
        $this->createStatement();
        return $this->statement->fetch(PDO::FETCH_ASSOC);
    }

    public function fetchObject(string $class)
    {
        $this->createStatement();
        return $this->statement->fetchObject($class);
    }

    public function executeSql(string $sql): QueryBuilder
    {
        $this->result = $this->dbc->query($sql);
        return $this;
    }

    public function getResults(): Collection
    {
        return new Collection($this->result->fetchAll(PDO::FETCH_ASSOC));
    }
}