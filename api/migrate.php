<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use App\Config\Database;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$pdo        = Database::connection();
$migrations = glob(__DIR__ . '/migrations/*.sql');

if ($migrations === false || count($migrations) === 0) {
    echo "No migration files found.\n";
    exit(0);
}

sort($migrations);

foreach ($migrations as $file) {
    $name = basename($file);
    $sql  = file_get_contents($file);

    if ($sql === false) {
        echo "  SKIP  {$name} (could not read file)\n";
        continue;
    }

    try {
        $pdo->exec($sql);
        echo "  OK    {$name}\n";
    } catch (\PDOException $e) {
        echo "  FAIL  {$name}: {$e->getMessage()}\n";
        exit(1);
    }
}

echo "\nMigrations complete.\n";
