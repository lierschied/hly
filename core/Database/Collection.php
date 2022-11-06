<?php

namespace Core\Database;

use Iterator;

class Collection implements Iterator
{
    public ?array $items;

    public function __construct(?array $items = [], private int $position = 0)
    {
        $this->items = $items;
    }

    public function first(): mixed
    {
        return array_shift($this->items);
    }

    public function current(): mixed
    {
        return $this->items[$this->position];
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function key(): mixed
    {
        return key($this->items[$this->position]);
    }

    public function valid(): bool
    {
        return isset($this->items[$this->position]);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }
}