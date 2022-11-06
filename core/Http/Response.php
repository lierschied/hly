<?php

namespace Core\Http;

use Core\Application\View;
use JsonException;
use Throwable;

class Response
{

    protected ResponseType $type = ResponseType::TEXT;
    protected mixed $body;
    protected ResponseCode $code = ResponseCode::OK;
    private array $headerList = [];

    /**
     * Sending HTTP header and body contents
     * @return void
     */
    public function send(): void
    {
        header($this->type->value);
        $this->setHeader();
        http_response_code($this->code->value);
        echo $this->body;
    }

    /**
     * Set the Response body
     * @throws JsonException
     */
    private function setBody(mixed $data): void
    {
        $this->body = $this->type->encode($data);
    }

    /**
     * Setting the response type e.g. application/json
     * @param ResponseType $type
     * @return $this
     */
    public function setType(ResponseType $type): static
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Is setting the actual response content
     * @throws JsonException
     */
    public function setResponse(mixed $body): void
    {
        $this->setBody($body);
    }

    /**
     * @param ResponseCode $code
     * @return void
     */
    public function setResponseCode(ResponseCode $code): void
    {
        $this->code = $code;
    }

    /**
     * Shorthand for setting Response type to json and adding JSON body
     * @throws JsonException
     */
    public function json(mixed $body): void
    {
        $this->setType(ResponseType::JSON)
            ->setResponse($body);
    }

    /**
     * Shorthand to directly render a view
     * @param mixed $view
     * @return void
     * @throws JsonException
     */
    public function view(mixed $view): void
    {
        try {
            $this->setResponse(View::render($view));
        } catch (Throwable $e) {
            $this->setResponse(sprintf('View %s not found', $view), ResponseCode::NOT_FOUND);
        }
    }

    public function addHeader(string $header, string $value): void
    {
        $this->headerList[$header] = $value;
    }

    private function setHeader(): void
    {
        array_walk($this->headerList, static fn($value, $header) => header("$header: $value"));
    }
}