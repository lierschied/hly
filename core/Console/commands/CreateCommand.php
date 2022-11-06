<?php

namespace Core\Console\commands;

use Core\Console\Cli;
use Core\Console\Command;
use Core\Console\CommandInterface;
use JetBrains\PhpStorm\NoReturn;

class CreateCommand extends Command implements CommandInterface
{
    private const TEMPLATE_COMMAND = <<<'EOF'
<?php

namespace Core\Console\commands;

use Core\Console\Cli;
use Core\Console\Command;
use Core\Console\CommandInterface;
use JetBrains\PhpStorm\NoReturn;

class {:?} extends Command implements CommandInterface
{
    #[NoReturn] public function run(): void
    {
        // TODO: Implement run() method.
    }

    public function validateArguments(): void
    {
        // TODO: Implement validateArguments() method.
    }

    public function help(): string
    {
        return "TODO: Implement help() method.";
    }
}
EOF;

    #[NoReturn] public function run(): void
    {
        $filename = ucfirst($this->arguments[0]);
        $filepath = __DIR__ . "/$filename.php";
        $template = str_replace('{:?}', $filename, self::TEMPLATE_COMMAND);
        Cli::createTemplateFile($filepath, $template);
        Cli::success("Created new Command: $filename");
    }

    public function validateArguments(): void
    {
        if (count($this->arguments) < 1) {
            Cli::error($this->help());
        }
    }

    public function help(): string
    {
        return "Missing arguments: create:command {name}";
    }
}