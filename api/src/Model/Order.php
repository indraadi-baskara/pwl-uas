<?php

declare(strict_types=1);

namespace App\Model;

use App\Config\Database;
use PDO;

final readonly class Order
{
    /**
     * @param list<OrderItem> $items
     */
    public function __construct(
        public int    $id,
        public int    $userId,
        public string $status,
        public int    $total,
        public array  $items,
        public string $createdAt,
        public string $updatedAt,
    ) {}

    /**
     * Creates an order from the user's current cart inside a transaction.
     * Throws RuntimeException if the cart is empty.
     */
    public static function createFromCart(int $userId): self
    {
        $pdo  = Database::connection();
        $cart = Cart::getWithItems($userId);

        if ($cart->items === []) {
            throw new \RuntimeException('Cart is empty');
        }

        $total = $cart->total();

        $pdo->beginTransaction();

        try {
            $orderStmt = $pdo->prepare(
                'INSERT INTO orders (user_id, status, total)
                 VALUES (:uid, \'pending\', :total)
                 RETURNING id, user_id, status, total, created_at, updated_at'
            );
            $orderStmt->execute([
                'uid'   => $userId,
                'total' => $total,
            ]);

            /** @var array<string, mixed> $orderRow */
            $orderRow = $orderStmt->fetch(PDO::FETCH_ASSOC);
            $orderId  = (int) $orderRow['id'];

            $itemStmt = $pdo->prepare(
                'INSERT INTO order_items (order_id, product_id, name, price, quantity)
                 VALUES (:order_id, :product_id, :name, :price, :quantity)'
            );

            foreach ($cart->items as $cartItem) {
                $itemStmt->execute([
                    'order_id'   => $orderId,
                    'product_id' => $cartItem->productId,
                    'name'       => $cartItem->productName,
                    'price'      => $cartItem->price,
                    'quantity'   => $cartItem->quantity,
                ]);
            }

            Cart::clearByUserId($userId);

            $pdo->commit();

            $items = self::loadItems($orderId);

            return new self(
                id:        $orderId,
                userId:    (int) $orderRow['user_id'],
                status:    (string) $orderRow['status'],
                total:     (int) $orderRow['total'],
                items:     $items,
                createdAt: (string) $orderRow['created_at'],
                updatedAt: (string) $orderRow['updated_at'],
            );
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function findById(int $id): ?self
    {
        $stmt = Database::connection()->prepare(
            'SELECT id, user_id, status, total, created_at, updated_at
               FROM orders
              WHERE id = :id
              LIMIT 1'
        );
        $stmt->execute(['id' => $id]);

        /** @var array<string, mixed>|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        $items = self::loadItems((int) $row['id']);

        return new self(
            id:        (int) $row['id'],
            userId:    (int) $row['user_id'],
            status:    (string) $row['status'],
            total:     (int) $row['total'],
            items:     $items,
            createdAt: (string) $row['created_at'],
            updatedAt: (string) $row['updated_at'],
        );
    }

    /**
     * @return array{data: list<self>, total: int}
     */
    public static function findByUser(int $userId, int $page, int $limit): array
    {
        $pdo    = Database::connection();
        $offset = ($page - 1) * $limit;

        $countStmt = $pdo->prepare(
            'SELECT COUNT(*) FROM orders WHERE user_id = :uid'
        );
        $countStmt->execute(['uid' => $userId]);
        $total = (int) $countStmt->fetchColumn();

        $dataStmt = $pdo->prepare(
            'SELECT id, user_id, status, total, created_at, updated_at
               FROM orders
              WHERE user_id = :uid
              ORDER BY created_at DESC
              LIMIT :limit OFFSET :offset'
        );
        $dataStmt->bindValue('uid',    $userId, PDO::PARAM_INT);
        $dataStmt->bindValue('limit',  $limit,  PDO::PARAM_INT);
        $dataStmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $dataStmt->execute();

        /** @var list<array<string, mixed>> $rows */
        $rows = $dataStmt->fetchAll(PDO::FETCH_ASSOC);

        $data = array_map(static fn (array $row) => new self(
            id:        (int) $row['id'],
            userId:    (int) $row['user_id'],
            status:    (string) $row['status'],
            total:     (int) $row['total'],
            items:     [],
            createdAt: (string) $row['created_at'],
            updatedAt: (string) $row['updated_at'],
        ), $rows);

        return ['data' => $data, 'total' => $total];
    }

    /**
     * @return array{data: list<self>, total: int}
     */
    public static function findAll(int $page, int $limit): array
    {
        $pdo    = Database::connection();
        $offset = ($page - 1) * $limit;

        $countStmt = $pdo->query('SELECT COUNT(*) FROM orders');

        if ($countStmt === false) {
            return ['data' => [], 'total' => 0];
        }

        $total = (int) $countStmt->fetchColumn();

        $dataStmt = $pdo->prepare(
            'SELECT id, user_id, status, total, created_at, updated_at
               FROM orders
              ORDER BY created_at DESC
              LIMIT :limit OFFSET :offset'
        );
        $dataStmt->bindValue('limit',  $limit,  PDO::PARAM_INT);
        $dataStmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $dataStmt->execute();

        /** @var list<array<string, mixed>> $rows */
        $rows = $dataStmt->fetchAll(PDO::FETCH_ASSOC);

        $data = array_map(static fn (array $row) => new self(
            id:        (int) $row['id'],
            userId:    (int) $row['user_id'],
            status:    (string) $row['status'],
            total:     (int) $row['total'],
            items:     [],
            createdAt: (string) $row['created_at'],
            updatedAt: (string) $row['updated_at'],
        ), $rows);

        return ['data' => $data, 'total' => $total];
    }

    public static function updateStatus(int $id, string $status): ?self
    {
        $stmt = Database::connection()->prepare(
            'UPDATE orders
                SET status     = :status,
                    updated_at = NOW()
              WHERE id = :id
          RETURNING id, user_id, status, total, created_at, updated_at'
        );
        $stmt->execute(['status' => $status, 'id' => $id]);

        /** @var array<string, mixed>|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        $items = self::loadItems((int) $row['id']);

        return new self(
            id:        (int) $row['id'],
            userId:    (int) $row['user_id'],
            status:    (string) $row['status'],
            total:     (int) $row['total'],
            items:     $items,
            createdAt: (string) $row['created_at'],
            updatedAt: (string) $row['updated_at'],
        );
    }

    /**
     * @return list<OrderItem>
     */
    public static function loadItems(int $orderId): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT id, order_id, product_id, name, price, quantity
               FROM order_items
              WHERE order_id = :oid
              ORDER BY id ASC'
        );
        $stmt->execute(['oid' => $orderId]);

        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(static fn (array $row) => OrderItem::fromRow($row), $rows);
    }
}
