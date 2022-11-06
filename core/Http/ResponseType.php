<?php

namespace Core\Http;

use JsonException;

enum ResponseType: string
{
    case TEXT = 'Content-Type: text/html';
    case JSON = 'Content-Type: json';

    /**
     * @throws JsonException
     */
    public function encode($body): string|false
    {
        return match ($this) {
            self::TEXT => $body,
            self::JSON => json_encode($body, JSON_THROW_ON_ERROR)
        };
    }
}