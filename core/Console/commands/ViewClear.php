<?php

namespace Core\Console\commands;

use Core\Console\Cli;
use Core\Console\Command;
use Core\Console\CommandInterface;
use JetBrains\PhpStorm\NoReturn;

class ViewClear extends Command implements CommandInterface
{
    #[NoReturn] public function run(): void
    {
        exec('rm -rf ' . __ROOT__ . '/misc/views/*.php');
    }

    public function validateArguments(): void
    {

    }

    public function help(): string
    {
        return "clears compiled views within misc/views";
    }
}