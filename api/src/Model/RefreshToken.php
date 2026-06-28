<?php

declare(strict_types=1);

namespace App\Model;

use App\Config\Database;
use PDO;

final class RefreshToken
{
    public function __construct(
        public readonly int    $id,
        public readonly int    $userId,
        public readonly string $tokenHash,
        public readonly string $expiresAt,
        public readonly bool   $revoked,
    ) {}

    /** @param array<string, mixed> $row */
    public static function fromRow(array $row): self
    {
        return new self(
            id:        (int) $row['id'],
            userId:    (int) $row['user_id'],
            tokenHash: (string) $row['token_hash'],
            expiresAt: (string) $row['expires_at'],
            revoked:   (bool) $row['revoked'],
        );
    }

    public static function create(int $userId, string $plainToken, int $ttlSeconds): string
    {
        $hash      = hash('sha256', $plainToken);
        $expiresAt = date('Y-m-d H:i:s', time() + $ttlSeconds);

        $stmt = Database::connection()->prepare(
            'INSERT INTO refresh_tokens (user_id, token_hash, expires_at)
             VALUES (:user_id, :token_hash, :expires_at)'
        );
        $stmt->execute(['user_id' => $userId, 'token_hash' => $hash, 'expires_at' => $expiresAt]);

        return $plainToken;
    }

    public static function findValid(string $plainToken): ?self
    {
        $hash = hash('sha256', $plainToken);

        $stmt = Database::connection()->prepare(
            "SELECT * FROM refresh_tokens
             WHERE token_hash = :hash
               AND revoked = FALSE
               AND expires_at > NOW()
             LIMIT 1"
        );
        $stmt->execute(['hash' => $hash]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row !== false ? self::fromRow($row) : null;
    }

    public static function revoke(string $plainToken): void
    {
        $hash = hash('sha256', $plainToken);

        $stmt = Database::connection()->prepare(
            'UPDATE refresh_tokens SET revoked = TRUE WHERE token_hash = :hash'
        );
        $stmt->execute(['hash' => $hash]);
    }

    public static function revokeAllForUser(int $userId): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE refresh_tokens SET revoked = TRUE WHERE user_id = :user_id'
        );
        $stmt->execute(['user_id' => $userId]);
    }

    public function isExpired(): bool
    {
        return strtotime($this->expiresAt) < time();
    }
}
