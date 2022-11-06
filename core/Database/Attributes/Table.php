<?php

namespace Core\Database\Attributes;

use Attribute;

#[Attribute]
class Table
{
    public function __construct(public string $name)
    {
    }
}