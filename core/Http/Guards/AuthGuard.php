<?php

namespace Core\Http\Guards;

class AuthGuard implements RouteGuard
{

    public static function verify(): bool
    {
        return session()->user()->isLoggedIn();
    }

    public static function onFailure(): void
    {
        redirect('/login');
    }
}