<?php

namespace Core\Shared;

use Core\Exceptions\ContainerException;

/**
 * @method static bool isGet()
 * @method static bool isPost()
 * @method static string getUrl()
 * @method static mixed getParam(string $key)
 * @method static string type()
 * @method static null|string getHeader(string $header, ?string $default = null)
 *
 * @see \Core\Http\Request
 */
class Request extends Shared
{
    protected static function getIdentifier(): string
    {
        return 'request';
    }

    /**
     * @throws ContainerException
     */
    public static function getInstance(): \Core\Http\Request
    {
        return self::$containerInstance->get(self::getIdentifier());
    }
}