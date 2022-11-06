<?php

namespace Core\Console;

class Command
{
    protected array $arguments;

    public function __construct(array $arguments)
    {
        $this->arguments = $arguments;
    }
}