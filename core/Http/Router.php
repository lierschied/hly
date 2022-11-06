<?php

namespace Core\Http;

use Core\Exceptions\HttpException;
use Core\Http\Attributes\Route;
use Core\Http\Attributes\Speedy;
use Core\Http\Guards\RouteGuard;
use Core\Shared\ClassMapper;
use Core\Shared\Request;
use Core\Shared\Response;
use ReflectionClass;
use ReflectionException;

class Router
{
    /**
     * Holds all available routes
     * @var array
     */
    public array $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => [],
    ];

    /**
     * Current active route
     * @var mixed|null
     */
    private mixed $activeRoute = null;

    /**
     * If routes are defined with placeholder e.g. /example/{a}/{b}
     * the regex will be stored within $regex[RequestMethod][routeName]
     * @var array
     */
    private array $regex = [];

    /**
     * @var RouteGuard[]
     */
    private array $currentGuards = [];

    public function __construct()
    {
    }

    /**
     * @throws ReflectionException
     * @throws HttpException
     */
    public function registerRoutes(): self
    {
        $classes = ClassMapper::classesWithinNamespace('app.controller');
        foreach ($classes as $className => $file) {
            $refClass = new ReflectionClass($className);
            $methods = $refClass->getMethods();
            foreach ($methods as $method) {
                $attributes = $method->getAttributes(Route::class);
                $attribute = array_shift($attributes);
                if ($attribute === null) {
                    continue;
                }
                $instance = $attribute->newInstance();
                $this->currentGuards = is_array($instance->guards) ? $instance->guards : [$instance->guards];
                $this->addRoute($instance->name, [$className, $method->getName()], $instance->requestMethod);
                $this->currentGuards = [];
            }
        }

        return $this;
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    public function resolve(): void
    {
        if ($this->routeExists()) {
            /* @var RouteGuard $guard */
            foreach ($this->activeRoute['guards'] as $guard) {
                if (!$guard::verify()) {
                    $guard::onFailure();
                }
            }

            $refClass = new ReflectionClass($this->activeRoute['callback'][0]);
            $refMethod = $refClass->getMethod($this->activeRoute['callback'][1]);
            $speedy = $refMethod->getAttributes(Speedy::class);
            !isset($speedy[0]) ?: $speedy[0]->newInstance();

            match ($this->activeRoute['handler']) {
                RouteAction::CONTROLLER => $this->handleController(),
                RouteAction::VIEW => $this->handleView(),
                RouteAction::CALLABLE => $this->handleCallable()
            };
        } else {
            Response::setResponse('Not found');
            Response::setResponseCode(ResponseCode::NOT_FOUND);
        }
    }

    /**
     * Creating new Controller and executing method from route active config
     * @return void
     */
    private function handleController(): void
    {
        $controller = new $this->activeRoute['callback'][0];
        $callback = $this->activeRoute['callback'][1];
        $this->setResponse($controller->$callback());
    }

    /**
     * @return void
     */
    private function handleView(): void
    {
        Response::view($this->activeRoute['callback']);
    }

    /**
     * Execute callable from route active config
     * @return void
     */
    private function handleCallable(): void
    {
        $this->setResponse($this->activeRoute['callback']());
    }

    /**
     * @param mixed $responseBody
     * @return void
     */
    private function setResponse(mixed $responseBody): void
    {
        if (is_array($responseBody)) {
            Response::json($responseBody);
        }
        Response::setResponse($responseBody);
    }

    /**
     * Can check if current/any route exists
     * @param string|null $url
     * @param string|null $type
     * @return bool
     */
    private function routeExists(?string $url = null, ?string $type = null): bool
    {
        if (array_key_exists($url ?? Request::getUrl(), $this->routes[$type ?? Request::type()])) {
            $this->activeRoute = $this->routes[Request::type()][Request::getUrl()];
            return true;
        }
        if (!array_key_exists(Request::type(), $this->regex)) {
            return false;
        }
        foreach ($this->regex[Request::type()] as $route => $regex) {
            if (preg_match('#^' . $regex . '$' . '#', Request::getUrl())) {
                $this->activeRoute = $this->routes[Request::type()][$route];
                return true;
            }
        }
        return false;
    }

    /**
     * Register a new GET route
     * @param string $route
     * @param string|array|callable $callback
     * @return void
     * @throws HttpException
     */
    public function get(string $route, string|array|callable $callback): void
    {
        $this->addRoute($route, $callback, RequestMethod::GET);
    }

    /**
     * Register a new POST route
     * @param string $route
     * @param string|array|callable $callback
     * @return void
     * @throws HttpException
     */
    public function post(string $route, string|array|callable $callback): void
    {
        $this->addRoute($route, $callback, RequestMethod::POST);
    }

    /**
     * Register a new PUT route
     * @param string $route
     * @param string|array|callable $callback
     * @return void
     * @throws HttpException
     */
    public function put(string $route, string|array|callable $callback): void
    {
        $this->addRoute($route, $callback, RequestMethod::PUT);
    }

    /**
     * Register a new DELETE route
     * @param string $route
     * @param string|array|callable $callback
     * @return void
     * @throws HttpException
     */
    public function delete(string $route, string|array|callable $callback): void
    {
        $this->addRoute($route, $callback, RequestMethod::DELETE);
    }

    /**
     * @return $this
     * @deprecated use php attributes & Router::registerRoutes()
     * loading routes defined within the routes folder
     */
    public function loadRoutes(): self
    {
        $dir = __DIR__ . '/../../routes/';
        $dirContent = scandir($dir);
        foreach ($dirContent as $file) {
            if (str_starts_with($file, '.')) {
                continue;
            }
            if (preg_match("/\w+\.php/", $file)) {
                include $dir . $file;
            }
        }
        return $this;
    }

    /**
     * @param string $route
     * @param callable|array|string $callback
     * @param RequestMethod[] $requestMethod
     * @return void
     * @throws HttpException
     */
    private function addRoute(string $route, callable|array|string $callback, RequestMethod|array $requestMethod): void
    {
        $routeHandler = match (true) {
            is_string($callback) => RouteAction::VIEW,
            is_array($callback) => RouteAction::CONTROLLER,
            is_callable($callback) => RouteAction::CALLABLE,
        };

        if (preg_match("/[^\w\/{}]/", $route, $m)) {
            throw new HttpException(sprintf("Invalid character %s within route definition", $m[0]));
        }

        if (is_array($requestMethod)) {
            foreach ($requestMethod as $method) {
                $this->insertRoute($route, $method, $routeHandler, $callback);
            }
        } else {
            $this->insertRoute($route, $requestMethod, $routeHandler, $callback);
        }
    }

    private function insertRoute(string $route, RequestMethod $requestMethod, RouteAction $routeHandler, callable|array|string $callback): void
    {
        if (preg_match("/{\w+}/", $route)) {
            $regex = str_replace('/', '\/', $route);
            $regex = preg_replace("/{\w+}/", '\w+', $regex);
            $this->regex[$requestMethod->value][$route] = $regex . "\/?";
        }

        $this->routes[$requestMethod->value][$route] = ['handler' => $routeHandler, 'callback' => $callback, 'guards' => $this->currentGuards];
    }

    /**
     * if not logged in, no routes surrounded by this method will be added, resulting in an 404
     *
     * @param array $guards
     * @param callable $callback
     * @return void
     */
    public function guard(array $guards, callable $callback): void
    {
        $this->currentGuards = $guards;
        $callback();
        $this->currentGuards = [];
    }
}