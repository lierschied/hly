<?php

namespace Core\Database\Migrations;

use Core\Database\Attributes\Column;
use ReflectionClass;
use ReflectionException;

class ModelParser
{
    private PropertyParser $properties;
    private array $columns = [];
    private ReflectionClass $reflectionClass;

    /**
     * @throws ReflectionException
     */
    public function __construct(private readonly string $name)
    {
        $this->reflectionClass = new ReflectionClass($this->name);
        $this->properties = new PropertyParser($this->reflectionClass);
    }

    public function getProperties(): mixed
    {
        return $this->properties;
    }

    public function columnsFromReflectionProperty(): array
    {
        foreach ($this->properties as $property) {
            $array = $property->getAttributes(Column::class);
            $column = array_shift($array);
            if ($column !== null) {
                $this->columns[toSnakeCase($property->getName())] = $column->newInstance()->asCreate();
            }
        }
        return $this->getColumns();
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getName(): string
    {
        return toSnakeCase($this->reflectionClass->getShortName());
    }

}