<?php

namespace Core\Console\commands;

use App\Models\User;
use Core\Console\Command;
use JetBrains\PhpStorm\NoReturn;

class Testing extends Command
{
    #[NoReturn] public function run(): void
    {
        $user = new User();
        $user->name = 'larry';
        $user->password = 'hans';
        $user->save();

        //   $userTable = new UserTable();
       // $userTable->createTable();
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