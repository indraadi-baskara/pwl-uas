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

    /** @param array<string, mixed>|list<mixed> $data */
    public static function success(array $data, int $status = 200): never
    {
        self::json(['status' => 'success', 'data' => $data], $status);
    }

    public static function error(string $message, int $status = 400): never
    {
        self::json(['status' => 'error', 'message' => $message], $status);
    }

    public static function notFound(string $message = 'Not Found'): never
    {
        self::error($message, 404);
    }

    public static function methodNotAllowed(): never
    {
        self::error('Method Not Allowed', 405);
    }

    public static function unauthorized(string $message = 'Unauthorized'): never
    {
        self::error($message, 401);
    }

    public static function forbidden(string $message = 'Forbidden'): never
    {
        self::error($message, 403);
    }
}
