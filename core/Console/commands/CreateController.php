<?php

namespace Core\Console\commands;

use Core\Console\Cli;
use Core\Console\Command;
use Core\Console\CommandInterface;
use JetBrains\PhpStorm\NoReturn;

class CreateController extends Command
{
    private const TEMPLATE_CONTROLLER = <<<'EOF'
<?php

namespace App\Controller;

class {:?}
{

}
EOF;

    #[NoReturn] public function run(): void
    {
        $filename = $this->arguments[0];
        if (!str_contains($filename, 'Controller')) {
            $filename .= 'Controller';
        }
        $filename = ucfirst($filename);
        $filepath = __ROOT__ . "/app/Controller/$filename.php";

        $template = str_replace('{:?}', $filename, self::TEMPLATE_CONTROLLER);
        Cli::createTemplateFile($filepath, $template);
        Cli::success("Created new Controller: $filename");
    }

    public function validateArguments(): void
    {
        if (count($this->arguments) < 1) {
            Cli::error($this->help());
        }
    }

    public function help(): string
    {
        return "Missing arguments: create:controller {name}";
    }
}