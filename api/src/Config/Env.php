<?php

declare(strict_types=1);

namespace App\Config;

final class Env
{
    public static function get(string $key): string
    {
        $value = $_ENV[$key] ?? getenv($key);

        if ($value === false || $value === '' || $value === null) {
            throw new \RuntimeException("Missing required env var: {$key}");
        }

        return (string) $value;
    }

    public static function int(string $key): int
    {
        return (int) self::get($key);
    }
}
