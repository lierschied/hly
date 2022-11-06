<?php

namespace Core\Http\Attributes;

use Attribute;
use Core\Shared\Request;
use Core\Shared\Response;

#[Attribute]
class Speedy
{
    private const VERSION = 'v0.0.1';

    public function __construct(public ?string $route = null, public ?string $title = null)
    {
        if ($this->route !== null && Request::getHeader('Speedy') !== self::VERSION) {
            redirect($this->route);
        }
        if ($this->title !== null) {
            Response::addHeader('Title', $this->title);
        }
        Response::addHeader('Speedy', self::VERSION);
    }
}