<?php

use Core\Kernel;
use Core\Shared\Response;

require_once __DIR__ . '/../vendor/autoload.php';

Kernel::getInstance()->run();
Response::send();