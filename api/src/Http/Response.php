<?php

declare(strict_types=1);

namespace App\Http;

final class Response
{
    /** @param array<string, mixed> $data */
    public static function json(array $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        exit;
    }

    public static function notFound(string $message = 'Not Found'): never
    {
        self::json(['error' => $message], 404);
    }

    public static function methodNotAllowed(): never
    {
        self::json(['error' => 'Method Not Allowed'], 405);
    }

    public static function unprocessable(string $message): never
    {
        self::json(['error' => $message], 422);
    }
}
