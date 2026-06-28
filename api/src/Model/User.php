<?php

declare(strict_types=1);

namespace App\Model;

use App\Config\Database;
use PDO;

final class User
{
    public function __construct(
        public readonly int    $id,
        public readonly string $email,
        public readonly string $passwordHash,
        public readonly string $role,
        public readonly string $createdAt,
    ) {}

    /** @param array<string, mixed> $row */
    public static function fromRow(array $row): self
    {
        return new self(
            id:           (int) $row['id'],
            email:        (string) $row['email'],
            passwordHash: (string) $row['password_hash'],
            role:         (string) $row['role'],
            createdAt:    (string) $row['created_at'],
        );
    }

    public static function findByEmail(string $email): ?self
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM users WHERE email = :email LIMIT 1'
        );
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row !== false ? self::fromRow($row) : null;
    }

    public static function findById(int $id): ?self
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM users WHERE id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row !== false ? self::fromRow($row) : null;
    }

    public static function create(string $email, string $password, string $role = 'user'): self
    {
        $hash = password_hash($password, PASSWORD_ARGON2ID);

        $stmt = Database::connection()->prepare(
            'INSERT INTO users (email, password_hash, role) VALUES (:email, :hash, :role) RETURNING *'
        );
        $stmt->execute(['email' => $email, 'hash' => $hash, 'role' => $role]);

        /** @var array<string, mixed> $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return self::fromRow($row);
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->passwordHash);
    }
}
