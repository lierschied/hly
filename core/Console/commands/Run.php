<?php

namespace Core\Console\commands;

use Core\Console\Cli;
use Core\Console\Command;
use Core\Console\CommandInterface;
use JetBrains\PhpStorm\NoReturn;

class Run extends Command
{

    #[NoReturn] public function run(): void
    {
        $args = implode(' ', $this->arguments);
        $result = exec("docker-compose $args");

        if ($result === false) {
            Cli::error("Something went wrong");
        }
    }

    public function validateArguments(): void
    {
        if (empty($this->arguments)) {
            Cli::error($this->help());
        }
    }

    public function help(): string
    {
        return "run {docker command}";
    }
}