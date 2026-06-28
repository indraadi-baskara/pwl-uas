<?php

declare(strict_types=1);

namespace App\Http;

final readonly class Request
{
    public string $method;
    public string $path;
    /** @var array<string, string> */
    public array $query;
    /** @var array<string, mixed> */
    public array $body;
    /** @var array<string, string> */
    public array $headers;

    public function __construct()
    {
        $this->method  = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $this->path    = strtok($_SERVER['REQUEST_URI'] ?? '/', '?') ?: '/';
        $this->query   = $_GET;
        $this->body    = $this->parseBody();
        $this->headers = $this->parseHeaders();
    }

    /** @return array<string, mixed> */
    private function parseBody(): array
    {
        $raw = file_get_contents('php://input');

        if ($raw === false || $raw === '') {
            return [];
        }

        $decoded = json_decode($raw, associative: true);

        return is_array($decoded) ? $decoded : [];
    }

    /** @return array<string, string> */
    private function parseHeaders(): array
    {
        $headers = [];

        foreach ($_SERVER as $key => $value) {
            if (!str_starts_with($key, 'HTTP_')) {
                continue;
            }

            $name           = str_replace('_', '-', substr($key, 5));
            $headers[$name] = (string) $value;
        }

        return $headers;
    }
}
