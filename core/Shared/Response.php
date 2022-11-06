<?php

namespace Core\Shared;

use Core\Exceptions\ContainerException;
use Core\Http\ResponseCode;
use Core\Http\ResponseType;

/**
 * @method static void send()
 * @method static static setType(ResponseType $type)
 * @method static void setResponse(mixed $body)
 * @method static void setResponseCode(ResponseCode $code)
 * @method static void json(mixed $body)
 * @method static void view(mixed $view)
 * @method static void addHeader(string $header, string $value)
 *
 * @see \Core\Http\Response
 */
class Response extends Shared
{
    public static function getIdentifier(): string
    {
        return 'response';
    }

    /**
     * @throws ContainerException
     */
    public static function getInstance(): \Core\Http\Response
    {
        return self::$containerInstance->get(self::getIdentifier());
    }
}