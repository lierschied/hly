<?php

namespace Core;

class ArrayHelper
{
    public static function get(array $array, ?string $dot, mixed $default = null)
    {
        if (is_null($dot)) {
            return $array;
        }

        if (array_key_exists($dot, $array)) {
            return $array[$dot];
        }

        foreach (explode('.', $dot) as $key) {
            if (array_key_exists($key, $array)) {
                $array = $array[$key];
            } else {
                return $default;
            }
        }

        return $array;
    }
}