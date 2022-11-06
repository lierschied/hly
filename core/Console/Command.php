<?php

namespace Core\Console;

abstract class Command implements CommandInterface
{
    protected array $arguments;

    public function __construct(array $arguments)
    {
        $this->arguments = $arguments;
    }
}