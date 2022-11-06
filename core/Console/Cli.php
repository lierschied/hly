<?php

namespace Core\Console;

use Core\Env;
use JetBrains\PhpStorm\NoReturn;

class Cli
{
    //STDOUT status codes
    public const EXIT_ERROR = 1;
    public const EXIT_SUCCESS = 0;
    //Ansi Codes
    private const ANSI_ESCAPE = "\033[";
    private const ANSI_RESET = "\0";

    private const CLASS_PREFIX = 'Core\\Console\\Commands\\';

    /**
     * The rest of $argv
     * First and second index of $argv will be removed
     * @var array
     */
    public array $arguments;

    /**
     * The first argument after the file name $argv[1]
     * Subcommands are used like command:subcommand
     * @var string
     */
    public string $command;

    public function __construct(array $argv)
    {
        Env::load();
        Env::set('DB_HOST', '127.0.0.1');
        array_shift($argv);
        if (empty($argv)) {
            self::error("No Command given!");
        }

        $this->command = array_shift($argv);
        $this->arguments = $argv;
    }

    /**
     * Entrypoint
     * @return void
     */
    #[NoReturn] public function process(): void
    {
        $this->command = str_replace(':', '', $this->command);

        $command_class = self::CLASS_PREFIX . $this->command;
        if (!class_exists($command_class)) {
            self::error("$this->command does not exist!");
        }

        /** @var CommandInterface $command */
        $command = new $command_class($this->arguments);
        $command->validateArguments();
        $command->run();
    }

    /**
     * Printout error message and exit with error code
     * @param string $message
     * @return void
     */
    #[NoReturn] public static function error(string $message): void
    {
        self::stdout("-- Error --", MessageType::Error);
        self::stdout($message, MessageType::Error);
        exit(self::EXIT_ERROR);
    }

    /**
     * Printout success message and exit with success code
     * @param string $message
     * @return void
     */
    #[NoReturn] public static function success(string $message): void
    {
        self::stdout($message, MessageType::Success);
        exit(self::EXIT_SUCCESS);
    }

    /**
     * Print to STDOUT
     * @param string $message
     * @param MessageType $type
     * @return void
     */
    public static function stdout(string $message, MessageType $type = MessageType::Default): void
    {
        $message = self::ANSI_ESCAPE . $type->value . $message . self::ANSI_RESET . PHP_EOL;

        fwrite(STDOUT, $message);
    }

    public static function confirm(string $message): bool
    {
        $answer = readline($message . " [yes|no]" . PHP_EOL);
        return preg_match("/yes|y/i", $answer);
    }

    /**
     * Create a template file
     * @param string $filepath
     * @param string $template
     * @return void
     */
    public static function createTemplateFile(string $filepath, string $template): void
    {
        if (is_file($filepath)) {
            self::error("$filepath already exists");
        }

        if (file_put_contents($filepath, $template) === false) {
            self::error("File $filepath could not be created!");
        }
    }

}