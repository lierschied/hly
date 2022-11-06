<?php

namespace Core\Shared;

use Core\Container\Container;
use Core\Exceptions\ContainerException;

abstract class Shared
{
    protected static Container $containerInstance;

    /**
     * Sets the current Container instance
     * @param $instance Container
     * @return void
     */
    public static function setContainerInstance(Container $instance): void
    {
        static::$containerInstance = $instance;
    }

    /**
     * Proxying a static Share call to the underlying service
     * each service needs to be registered in the container instance to be accessible here
     * @param string $name name function to call from a registered service
     * @param array $arguments
     * @return mixed
     * @throws ContainerException
     * @example
     * \Core\Shared\Router::get('/', [ExampleController::class, 'index'])
     * is forwarded like
     * $instance->get('router')->get('/', [ExampleController::class, 'index']);
     */
    public static function __callStatic(string $name, array $arguments)
    {
        return static::$containerInstance
            ->get(static::getIdentifier())
            ->$name(...$arguments);
    }
}