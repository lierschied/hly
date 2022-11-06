<?php

namespace Core\Container;

use Core\ClassMapper;
use Core\Config;
use Core\Database\Orm;
use Core\Exceptions\ContainerException;
use Core\Http\Request;
use Core\Http\Response;
use Core\Http\Router;

class Container
{
    /**
     * Holds all the available services
     * @var array
     */
    protected array $container = [];

    /**
     * @param array $services
     * @throws ContainerException
     */
    public function __construct(array $services = [])
    {
        foreach ($services as $alias => $class) {
            $this->set($alias, $class);
        }
    }

    /**
     * Set a new service
     * @param string $alias name/alias of the service
     * @param string $class Classname of the service
     * @return void
     * @throws ContainerException
     */
    public function set(string $alias, string $class): void
    {
        if (array_key_exists($alias, $this->container)) {
            throw new ContainerException("Service $alias already exists");
        }
        $this->container[$alias] = new $class();
    }

    /**
     * Get a service by his name/alias
     * @param string $alias
     * @return mixed
     * @throws ContainerException
     */
    public function get(string $alias): mixed
    {
        if (!array_key_exists($alias, $this->container)) {
            throw new ContainerException("$alias not found in Container");
        }
        return $this->container[$alias];
    }

    /**
     * Registers the default services
     * @param array $services
     * @return void
     * @throws ContainerException
     */
    public function registerDefault(array $services = []): void
    {
        $defaultServices = [
            'router' => Router::class,
            'request' => Request::class,
            'response' => Response::class,
            'orm' => Orm::class,
            'classMapper' => ClassMapper::class,
            'config' => Config::class,
        ];
        $services = [...$services, ...$defaultServices];
        foreach ($services as $alias => $class) {
            $this->set($alias, $class);
        }
    }
}