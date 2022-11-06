<?php

namespace Core\Console\commands;

use Core\ClassMapper;
use Core\Console\Command;
use Core\Console\CommandInterface;
use Core\Shared\Shared;
use JetBrains\PhpStorm\NoReturn;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use RuntimeException;

class NewShared extends Command
{
    private ClassMapper $classMapper;

    /**
     * @throws ReflectionException
     */
    #[NoReturn] public function run(): void
    {
        $this->classMapper = new ClassMapper();
        $this->classMapper->map(['Core\\Shared' => 'core/shared']);
        $sharedClasses = $this->classMapper->classesWithinNamespace('Core\\Shared\\');
        unset($sharedClasses[Shared::class]);

        foreach ($sharedClasses as $class => $file) {
            $refClass = new ReflectionClass($class);
            $docComment = $refClass->getDocComment();
            preg_match("/@see\s(.+)\n/", $docComment, $realClass);
            if (!isset($realClass[1])) {
                throw new  RuntimeException(sprintf('Unable to find real class through @see reference within class: %s', $class));
            }

            $realRefClass = new ReflectionClass($realClass[1]);
            $methods = $realRefClass->getMethods(ReflectionMethod::IS_PUBLIC);
            $methods = array_filter($methods, static fn($v) => $v->getName() !== '__construct');
            foreach ($methods as $method) {
                $returnType = str_replace('?', 'null|', $method->getReturnType());
                $shortName = $method->getName();

                if (!str_contains($docComment, "@method static returnType $shortName")) {
                    echo "@method static $returnType $shortName" . PHP_EOL;
                }
            }
        }
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