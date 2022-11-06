<?php

namespace Core\Database\Attributes;

use Attribute;
use Core\Database\DataType;

#[Attribute]
class Column
{
    public function __construct(
        public DataType $type = DataType::VARCHAR,
        public bool     $nullable = false,
        public ?string  $default = null)
    {
    }

    public function getDataType(): string
    {
        return $this->type->value;
    }

    public function asCreate(): string
    {
        $create = $this->type->value;
        if ($this->default !== null) {
            $create .= " DEFAULT $this->default";
        } elseif (!$this->nullable) {
            $create .= " NOT NULL";
        }
        return $create;
    }
}