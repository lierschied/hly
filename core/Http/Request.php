<?php

namespace Core\Http;

class Request
{
    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';
    private string $url;
    /**
     * @var array[]
     */
    private array $params;
    private array|false $headers;

    public function __construct()
    {
        $this->params = [...$_GET, ...$_POST];
        $this->url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->headers = getallheaders();
        session()->set('previous_url', $this->url);
    }

    /**
     * @return bool REQUEST_METHOD is GET
     */
    public function isGet(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === self::METHOD_GET;
    }

    /**
     * @return bool REQUEST_METHOD is POST
     */
    public function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === self::METHOD_POST;
    }

    /**
     * @return string current url
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    public function getParam(string $key): mixed
    {
        return $this->params[$key] ?? null;
    }

    /**
     * @return string REQUEST_METHOD
     */
    public function type(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function getHeader(string $header, ?string $default = null): ?string
    {
        return $this->headers[$header] ?? $default;
    }
}