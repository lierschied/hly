<?php

namespace Core\Console\commands;

use Core\ClassMapper;
use Core\Console\Cli;
use Core\Console\Command;
use Core\Console\CommandInterface;
use Core\Container\Container;
use Core\Exceptions\ContainerException;
use Core\Exceptions\HttpException;
use Core\Http\RouteAction;
use Core\Shared\Router;
use Core\Shared\Shared;
use JetBrains\PhpStorm\NoReturn;
use ReflectionException;

class DebugRoutes extends Command
{
    /**
     * @throws ReflectionException
     * @throws HttpException
     * @throws ContainerException
     */
    #[NoReturn] public function run(): void
    {
        if (!Cli::confirm('continue?')) { Cli::error('aborted!');}
        Shared::setContainerInstance(new Container(['router' => \Core\Http\Router::class, 'classMapper' => ClassMapper::class]));
        \Core\Shared\ClassMapper::map(['App\\Controller' => 'app/Controller']);
        $router = Router::getInstance();
        $router->registerRoutes();

        if (!empty($this->arguments[0])) {
            $method = strtoupper($this->arguments[0]);
            $router->routes = array_filter($router->routes, static fn($key) => $key === $method, ARRAY_FILTER_USE_KEY);
        }

        foreach ($router->routes as $method => $routes) {
            Cli::stdout($method);
            Cli::stdout('________');
            foreach ($routes as $route => $params) {
                $string = match ($params['handler']) {
                    RouteAction::VIEW => "View:" . $params['callback'],
                    RouteAction::CALLABLE => 'Callable: ',
                    RouteAction::CONTROLLER => $params['callback'][0] . "::" . $params['callback'][1]
                };
                Cli::stdout(sprintf("+ %s", $route));
                Cli::stdout(sprintf(" -> %s\n", $string));
            }
        }
    }

    public function validateArguments(): void
    {
        if (empty($this->arguments[0])) {
            return;
        }

        if (!preg_match("/GET|POST|PUT|DELETE/i", $this->arguments[0])) {
            Cli::error('Accepted methods: GET|POST|PUT|DELETE');
        }
    }

    public function help(): string
    {
        return "debug:routes [GET|POST|PUT|DELETE]";
    }
}