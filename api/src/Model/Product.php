<?php

declare(strict_types=1);

namespace App\Model;

use App\Config\Database;
use PDO;

final class Product
{
    public function __construct(
        public readonly int     $id,
        public readonly string  $name,
        public readonly ?string $description,
        public readonly int     $price,
        public readonly int     $stock,
        public readonly ?string $category,
        public readonly ?string $imagePath,
        public readonly string  $createdAt,
        public readonly string  $updatedAt,
    ) {}

    /** @param array<string, mixed> $row */
    public static function fromRow(array $row): self
    {
        return new self(
            id:          (int) $row['id'],
            name:        (string) $row['name'],
            description: isset($row['description']) ? (string) $row['description'] : null,
            price:       (int) $row['price'],
            stock:       (int) $row['stock'],
            category:    isset($row['category']) ? (string) $row['category'] : null,
            imagePath:   isset($row['image_path']) ? (string) $row['image_path'] : null,
            createdAt:   (string) $row['created_at'],
            updatedAt:   (string) $row['updated_at'],
        );
    }

    public function imageUrl(): ?string
    {
        if ($this->imagePath === null) {
            return null;
        }

        $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';

        return "{$scheme}://{$host}/uploads/{$this->imagePath}";
    }

    /**
     * @return array{data: list<self>, total: int}
     */
    public static function findAll(
        int $page = 1,
        int $limit = 10,
        string $search = '',
        string $category = '',
        ?int $minPrice = null,
        ?int $maxPrice = null,
    ): array {
        $offset = ($page - 1) * $limit;
        $params = [];
        $where  = [];

        if ($search !== '') {
            $where[]          = 'name ILIKE :search';
            $params['search'] = '%' . $search . '%';
        }

        if ($category !== '') {
            $where[]            = 'category = :category';
            $params['category'] = $category;
        }

        if ($minPrice !== null) {
            $where[]              = 'price >= :min_price';
            $params['min_price']  = $minPrice;
        }

        if ($maxPrice !== null) {
            $where[]              = 'price <= :max_price';
            $params['max_price']  = $maxPrice;
        }

        $whereClause = $where !== [] ? 'WHERE ' . implode(' AND ', $where) : '';

        $countStmt = Database::connection()->prepare(
            "SELECT COUNT(*) FROM products {$whereClause}"
        );
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $dataStmt = Database::connection()->prepare(
            "SELECT * FROM products {$whereClause}
             ORDER BY created_at DESC
             LIMIT :limit OFFSET :offset"
        );

        foreach ($params as $key => $value) {
            $dataStmt->bindValue($key, $value);
        }

        $dataStmt->bindValue('limit',  $limit,  PDO::PARAM_INT);
        $dataStmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $dataStmt->execute();

        $rows = $dataStmt->fetchAll(PDO::FETCH_ASSOC);
        $data = array_map(static fn ($row) => self::fromRow($row), $rows);

        return ['data' => $data, 'total' => $total];
    }

    public static function findById(int $id): ?self
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM products WHERE id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row !== false ? self::fromRow($row) : null;
    }

    /** @param array<string, mixed> $data */
    public static function create(array $data): self
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO products (name, description, price, stock, category, image_path)
             VALUES (:name, :description, :price, :stock, :category, :image_path)
             RETURNING *'
        );
        $stmt->execute([
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'price'       => (int) $data['price'],
            'stock'       => (int) ($data['stock'] ?? 0),
            'category'    => $data['category'] ?? null,
            'image_path'  => $data['image_path'] ?? null,
        ]);

        /** @var array<string, mixed> $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return self::fromRow($row);
    }

    /** @param array<string, mixed> $data */
    public static function update(int $id, array $data): ?self
    {
        $fields = [];
        $params = ['id' => $id];

        foreach (['name', 'description', 'price', 'stock', 'category', 'image_path'] as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "{$field} = :{$field}";
                $params[$field] = $data[$field];
            }
        }

        if ($fields === []) {
            return self::findById($id);
        }

        $fields[] = 'updated_at = NOW()';
        $set      = implode(', ', $fields);

        $stmt = Database::connection()->prepare(
            "UPDATE products SET {$set} WHERE id = :id RETURNING *"
        );
        $stmt->execute($params);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row !== false ? self::fromRow($row) : null;
    }

    public static function delete(int $id): bool
    {
        $stmt = Database::connection()->prepare(
            'DELETE FROM products WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
    }
}
