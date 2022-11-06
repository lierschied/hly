<?php

namespace Core\Console;

enum MessageType: string
{
    case Error = '31m';
    case Success = '32m';
    case Warning = '33m';
    case Default = '39m';
}