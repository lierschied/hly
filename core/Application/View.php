<?php

namespace Core\Application;

use InvalidArgumentException;

class View
{
    public static function render(string $view, array $data = []): string
    {
        $view = ucfirst($view);

        $viewPath = __ROOT__ . '/app/views/' . $view . '.hly';
        if (!is_file($viewPath)) {
            throw new InvalidArgumentException(sprintf("View %s not found", $view));
        }

        $renderedViewPath = __ROOT__ . '/misc/views/' . cached_filename($viewPath) . '.php';
        if (is_file($renderedViewPath)) {
            return self::load($renderedViewPath, $data);
        }

        $file = file_get_contents($viewPath);
        $file = ViewCompiler::compile($file);
        file_put_contents($renderedViewPath, $file);

        return self::load($renderedViewPath, $data);
    }

    private static function load(string $__filepath, array $__data): string
    {
        foreach ($__data as $__key => $__value) {
            $$__key = $__value;
        }
        ob_start();
        include $__filepath;
        return ob_get_clean();
    }
}