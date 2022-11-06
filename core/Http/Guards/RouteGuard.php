<?php

namespace Core\Http\Guards;

interface RouteGuard
{
    public static function verify(): bool;
    public static function onFailure(): void;
}