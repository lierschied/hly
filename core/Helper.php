<?php

use Core\Application\View;
use Core\Http\Request;
use Core\Http\Router;
use Core\Kernel;
use Core\Session;
use JetBrains\PhpStorm\NoReturn;

define("__ROOT__", dirname(__DIR__));

if (!function_exists('dd')) {
    #[NoReturn] function dd(mixed $data): void
    {
        highlight_string("<?php\n\$data =\n" . var_export($data, true) . ";\n?>");
        die();
    }
}

if (!function_exists('root')) {
    function root(): string
    {
        return __ROOT__;
    }
}


if (!function_exists('files_identical')) {
    function files_identical($fileA, $fileB): bool
    {
        if (filesize($fileA) !== filesize($fileB)) {
            return false;
        }

        $pointerA = fopen($fileA, 'rb');
        $pointerB = fopen($fileB, 'rb');

        $identical = true;
        while (!feof($pointerA)) {
            if (fread($pointerA, 8192) !== fread($pointerB, 8192)) {
                $identical = false;
                break;
            }
        }
        fclose($pointerA);
        fclose($pointerB);
        return $identical;
    }
}

if (!function_exists('cached_filename')) {
    function cached_filename(string $file): string
    {
        return md5(filesize($file) . filemtime($file));
    }
}

if (!function_exists('session')) {
    function session(): Session
    {
        return Session::getInstance();
    }
}

if (!function_exists('router')) {
    function router(): Router
    {
        return Kernel::getInstance()->container->get('router');
    }
}

if (!function_exists('request')) {
    function request(): Request
    {
        return Kernel::getInstance()->container->get('request');
    }
}

if (!function_exists('redirect')) {
    function redirect(string $to): void
    {
        header("Location: $to");
    }
}

if (!function_exists('view')) {
    function view(string $view, array $data = []): string
    {
        return View::render($view, $data);
    }
}

if (!function_exists('toSnakeCase')) {
    function toSnakeCase(string $string): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }
}