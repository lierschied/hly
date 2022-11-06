<?php

namespace Core\Shared;

use Core\Exceptions\ContainerException;

/**
 * @method static self registerRoutes()
 * @method static void resolve()
 * @method static void get(string $route, string|array|callable $callback)
 * @method static void post(string $route, string|array|callable $callback)
 * @method static void put(string $route, string|array|callable $callback)
 * @method static void delete(string $route, string|array|callable $callback)
 * @method static self loadRoutes()
 * @method static void guard(array $guards, callable $callback)
 *
 * @see \Core\Http\Router
 */
class Router extends Shared
{
    public static function getIdentifier(): string
    {
        return 'router';
    }

    /**
     * @throws ContainerException
     */
    public static function getInstance(): \Core\Http\Router
    {
        return self::$containerInstance->get(self::getIdentifier());
    }
}