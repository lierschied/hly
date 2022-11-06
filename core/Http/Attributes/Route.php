<?php

namespace Core\Http\Attributes;

use Attribute;
use Core\Http\Guards\RouteGuard;
use Core\Http\RequestMethod;

#[Attribute]
class Route
{
    /**
     * @param string $name
     * @param RequestMethod[] $requestMethod
     * @param RouteGuard[] $guards
     */
    public function __construct(public string $name, public RequestMethod|array $requestMethod = RequestMethod::GET, public string|array $guards = [])
    {
    }
}