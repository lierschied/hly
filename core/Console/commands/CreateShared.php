<?php

namespace Core\Console\commands;

use Core\Console\Cli;
use Core\Console\Command;
use Core\Console\CommandInterface;
use JetBrains\PhpStorm\NoReturn;

class CreateShared extends Command implements CommandInterface
{

    #[NoReturn] public function run(): void
    {
        $spaces = explode('/', trim($this->arguments[0]));
        $filename = end($spaces);
        $nameSpace = implode('\\', $spaces);
        $filepath = __DIR__ . "/../../shared/$filename.php";
        $templateFile = file_get_contents(__Dir__ . '/templates/Share.template');
        $template = str_replace(['{%namespace%}', '{%name%}', '{%alias%}'], [$nameSpace, $filename, strtolower($filename)], $templateFile);
        Cli::createTemplateFile($filepath, $template);

        Cli::success("Created new Share: $filename");
    }

    public function validateArguments(): void
    {
        if (empty($this->arguments[0])) {
            Cli::error("Please specify a filename");
        }
    }

    public function help(): string
    {
        return "shared:generate {to/namespace}";
    }
}