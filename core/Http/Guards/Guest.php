<?php

namespace Core\Http\Guards;

class Guest implements RouteGuard
{

    public static function verify(): bool
    {
        return !session()->user()->isLoggedIn();
    }

    public static function onFailure(): void
    {
        redirect(session()->get('previousUrl', '/'));
    }
}