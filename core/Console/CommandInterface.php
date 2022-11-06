<?php

namespace Core\Console;

use JetBrains\PhpStorm\NoReturn;

interface CommandInterface
{

    public function __construct(array $arguments);

    #[NoReturn] public function run(): void;

    public function validateArguments(): void;

    public function help(): string;
}