<?php

namespace Core\Console\commands;

use Core\Console\Cli;
use Core\Console\Command;
use Core\Console\CommandInterface;
use JetBrains\PhpStorm\NoReturn;

class SharedGenerate extends Command
{
    private const PATH = __DIR__ . '/../../shared';
    private const ROOT_PATH = __DIR__ . '/../../..';
    private const EXCLUDED_FILES = ['.', '..', 'Shared.php'];

    #[NoReturn] public function run(): void
    {
        $dir = scandir(self::PATH);
        $dir = array_filter($dir, static fn($f) => !in_array($f, self::EXCLUDED_FILES, true));
        foreach ($dir as $fileName) {
            //extract original file name from @see tag
            $sharedFilename = self::PATH . "/$fileName";
            $sharedFile = file_get_contents($sharedFilename);
            if (!preg_match("/@see\s(.+)\n/", $sharedFile, $match) || !array_key_exists(1, $match)) {
                Cli::error('Unable to find "@see" comment');
            }
            $see = $match[1];
            //transform namespace to filepath
            $original = str_replace('\\', '/', $see);
            $originalFile = file_get_contents(self::ROOT_PATH . $original . '.php');

            //find all public functions
            // 0 => complete match e.g.: public function test(string $str): bool
            // 1 => function name and arguments e.g.: test(string $str)
            // 2 => function return type e.g.: : bool
            preg_match_all("/public function (\w+\(.*\))(:.+)?/", $originalFile, $publicFunctions);

            //create new docblock for Shared file
            $docBlock = '/**' . PHP_EOL;
            $imports = '';
            foreach ($publicFunctions[1] as $key => $method) {
                //ignore __construct or __callStatic etc.
                if (str_starts_with($method, '__')) {
                    continue;
                }

                //try catching non default parameter type
                if (preg_match_all('/\W([A-Z]\w+) \$/', $method, $types)) {
                    foreach ($types[1] as $type) {
                        //search for use statement within original file
                        if (preg_match("/use .+$type;/", $originalFile, $matchedType)) {
                            $imports .= $matchedType[0] . PHP_EOL;
                        } else {
                            //if no use statement is found, use the same namespace as the original file
                            //replace \Class from the $see docBlock attribute, trim the leading \
                            $use = sprintf("use %s;\n", trim(preg_replace('/\\w+$/', $type, $see), '\\'));
                            //only add use statement if it is not already there
                            if (!str_contains($imports, $use) && !str_contains($sharedFile, $use)) {
                                $imports .= $use;
                            }
                        }
                    }
                }
                $returnType = str_replace(': ', '', $publicFunctions[2][$key]);
                if (str_starts_with($returnType, '?')) {
                    $returnType = str_replace('?', 'null|', $returnType);
                }
                $docBlock .= " * @method static $returnType $method" . PHP_EOL;
            }
            $docBlock = sprintf("%s *\n * @see %s\n */", $docBlock, $see);
            //conditionally remove leading line break to keep the format clean
            $r = '';
            if (!empty($imports)) {
                $docBlock = sprintf("%s\n%s", $imports, $docBlock);
                $r = '\n?';
            }

            //replace the current docblock with the new one
            //search for /** ... */
            $sharedFile = preg_replace("/$r\/\*\*.+(?=\nclass)/s", $docBlock, $sharedFile);
            file_put_contents($sharedFilename, $sharedFile);
        }

        Cli::success(sprintf("Generated DocBlocks for:\n+ %s", implode(PHP_EOL . '+ ', $dir)));
    }

    public function validateArguments(): void
    {
        //no arguments required
    }

    public function help(): string
    {
        return "shared:generate";
    }
}