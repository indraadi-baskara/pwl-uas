<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use App\Config\Database;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$pdo = Database::connection();

$admins = [
    ['email' => 'admin@example.com', 'password' => 'password123'],
];

foreach ($admins as $admin) {
    $exists = $pdo->prepare('SELECT id FROM users WHERE email = :email');
    $exists->execute(['email' => $admin['email']]);

    if ($exists->fetch()) {
        echo "  SKIP  {$admin['email']} (already exists)\n";
        continue;
    }

    $hash = password_hash($admin['password'], PASSWORD_ARGON2ID);

    $pdo->prepare(
        'INSERT INTO users (email, password_hash, role) VALUES (:email, :hash, :role)'
    )->execute([
        'email' => $admin['email'],
        'hash'  => $hash,
        'role'  => 'admin',
    ]);

    echo "  OK    {$admin['email']} (password: {$admin['password']})\n";
}

echo "\nSeeding complete.\n";
