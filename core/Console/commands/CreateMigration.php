<?php

namespace Core\Console\commands;

use Core\Console\Command;
use Core\Console\CommandInterface;
use Core\Database\Migrations\Migrator;
use Core\Env;
use JetBrains\PhpStorm\NoReturn;
use ReflectionException;


class CreateMigration extends Command
{
    /**
     * @throws ReflectionException
     */
    #[NoReturn] public function run(): void
    {
        $migrator = new Migrator();
        $sql = $migrator->generateSql();

        $filename = time() . ".sql";
        $path = __ROOT__ . '/db/migrations/';
        //file_put_contents($path . $filename, $sql);

    }

    public function validateArguments(): void
    {
    }

    public function help(): string
    {
        return "create:migration";
    }

}