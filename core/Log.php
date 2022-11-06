<?php

namespace Core;

class Log
{
    public static function log($message): void
    {
        $logfile = __DIR__ . '/../logs/' . date('Y_m_d_H') . '-log.log';
        error_log($message . PHP_EOL, 3, $logfile);
    }
}