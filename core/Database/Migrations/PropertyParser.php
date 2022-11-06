<?php

namespace Core\Database\Migrations;

use Iterator;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

class PropertyParser implements Iterator
{
    private int $position;
    private ?ReflectionProperty $currentProperty = null;
    private array $properties;

    /**
     * @throws ReflectionException
     */
    public function __construct(private ReflectionClass $refClass)
    {
        $this->position = 0;
        $props = $refClass->getProperties();
        $this->properties = array_filter($props, static fn($v) => $v !== null);

        !isset($this->properties[0]) ?: $this->currentProperty = $this->properties[0];
    }

    public function current(): ?ReflectionProperty
    {
        return $this->currentProperty;
    }

    public function next(): void
    {
        $this->currentProperty = $this->properties[$this->position];
        ++$this->position;
    }

    public function key(): string
    {
        return $this->currentProperty->getName();
    }

    public function valid(): bool
    {
        return isset($this->properties[$this->position]);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }
}