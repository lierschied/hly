<?php

namespace Core\Shared;

use Core\Exceptions\ContainerException;

/**
 * @method static null|array getConfig(string $domain)
 * @method static string|array|null get(string $domain, string $key, string|array|null $default = null)
 *
 * @see \Core\Config
 */
class Config extends Shared
{
    public static function getIdentifier(): string
    {
        return 'config';
    }

    /**
     * @throws ContainerException
     */
    public static function get_instance(): \Core\Config
    {
        return self::$containerInstance->get(self::getIdentifier());
    }
}