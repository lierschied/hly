<?php

namespace Core;

use InvalidArgumentException;

class Env
{
    /**
     * Loading env variables from a .env file
     * @param string $path
     * @return void
     */
    public static function load(string $path = __DIR__ . "/../.env"): void
    {
        if (!file_exists($path)) {
            throw new InvalidArgumentException(sprintf('%s does not exist', $path));
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if (str_starts_with($line, '#')) {
                continue;
            }
            putenv($line);
        }
    }

    /**
     * Set a Key=Value env variable
     * @param string $key
     * @param string $value
     * @return void
     */
    public static function set(string $key, string $value): void
    {
        $key = strtoupper(trim($key));
        $value = trim($value);

        if (preg_match("/[^\w.]/", $key) || preg_match("/[^\w.]]/", $value)) {
            throw new InvalidArgumentException(sprintf('%s=%s env contains invalid characters', $key, $value));
        }

        putenv("$key=$value");
    }

    /**
     * Get an env variable or default the value, otherwise throw an InvalidArgumentException
     * @param string $key
     * @param string $default
     * @return string
     */
    public static function get(string $key, string $default = ''): string
    {
        $value = getenv($key);
        if ($value === false && $default === '') {
            throw new InvalidArgumentException(sprintf("Env variable %s does not exist and no default is defined", $key));
        }
        return $value === false ? $default : $value;
    }

    /**
     * Alias
     * @return array
     * @see getenv()
     */
    public static function getAll(): array
    {
        return getenv();
    }
}