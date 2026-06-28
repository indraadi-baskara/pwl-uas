<?php

declare(strict_types=1);

namespace App\Service;

use App\Config\Database;

final class RateLimiter
{
    private const WINDOW_SECONDS = 60;
    private const MAX_ATTEMPTS   = 10;

    public static function check(string $key): bool
    {
        $pdo = Database::connection();

        // Remove stale windows
        $pdo->prepare(
            "DELETE FROM rate_limits
             WHERE key = :key AND window_start < NOW() - INTERVAL '1 minute'"
        )->execute(['key' => $key]);

        $stmt = $pdo->prepare(
            'SELECT id, attempts FROM rate_limits WHERE key = :key LIMIT 1'
        );
        $stmt->execute(['key' => $key]);
        $row = $stmt->fetch();

        if ($row === false) {
            $pdo->prepare(
                'INSERT INTO rate_limits (key) VALUES (:key)'
            )->execute(['key' => $key]);
            return true;
        }

        if ((int) $row['attempts'] >= self::MAX_ATTEMPTS) {
            return false;
        }

        $pdo->prepare(
            'UPDATE rate_limits SET attempts = attempts + 1 WHERE id = :id'
        )->execute(['id' => $row['id']]);

        return true;
    }
}
