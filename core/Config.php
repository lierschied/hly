<?php

namespace Core;

use function Composer\Autoload\includeFile;

class Config
{
    private array $config;

    public function __construct()
    {
        $this->config['app'] = require __ROOT__ . '/config/app.php';
    }

    public function getConfig(string $domain): ?array
    {
        return $this->config[$domain] ?? null;
    }

    public function get(string $domain, string $key, string|array|null $default = null): string|array|null
    {
        $key = "$domain.$key";
        return ArrayHelper::get($this->config, $key, $default);
    }
}