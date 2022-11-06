<?php

namespace Core\Http;

enum RouteAction
{
    case CONTROLLER;
    case VIEW;
    case CALLABLE;
}