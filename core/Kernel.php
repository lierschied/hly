<?php

namespace Core;

use Core\Container\Container;
use Core\Exceptions\ContainerException;
use Core\Shared\Router;
use Core\Shared\Shared;

class Kernel
{
    private static ?Kernel $instance = null;
    /**
     * Holds all registered services
     * @var Container
     */
    public Container $container;

    /**
     * @throws ContainerException
     */
    private function __construct()
    {
        Env::load();
        Session::getInstance()->start();
        $this->container = new Container();
        $this->container->registerDefault();
        Shared::setContainerInstance($this->container);
    }

    /**
     * Entrypoint to the core application
     * @return void
     */
    public function run(): void
    {
        \Core\Shared\ClassMapper::map();
        Router::registerRoutes()->resolve();
    }

    /**
     * Singleton access
     * @return Kernel
     */
    public static function getInstance(): Kernel
    {
        return self::$instance ?? self::$instance = new Kernel();
    }
}