<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Auth;
use App\Http\Request;
use App\Http\Response;
use App\Model\Order;
use App\Model\OrderItem;
use App\Validation\Validator;
use OpenApi\Attributes as OA;

final class OrderController
{
    /** @var list<string> */
    private const VALID_STATUSES = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];

    #[OA\Get(
        path: '/orders',
        operationId: 'listOrders',
        summary: 'List orders — all orders for admin, own orders for customers',
        tags: ['Orders'],
        parameters: [
            new OA\Parameter(name: 'page',  in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'limit', in: 'query', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Paginated list of orders'),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ],
    )]
    public function index(Request $request): never
    {
        $auth  = Auth::requireAuth();
        $page  = max(1, (int) ($request->query['page']  ?? 1));
        $limit = min(100, max(1, (int) ($request->query['limit'] ?? 10)));

        if ($auth['role'] === 'admin') {
            $result = Order::findAll($page, $limit);
        } else {
            $result = Order::findByUser($auth['sub'], $page, $limit);
        }

        $data = array_map(fn (Order $o) => $this->toArray($o), $result['data']);

        Response::paginated($data, [
            'total'        => $result['total'],
            'per_page'     => $limit,
            'current_page' => $page,
            'last_page'    => (int) ceil($result['total'] / max(1, $limit)),
        ]);
    }

    #[OA\Get(
        path: '/orders/{id}',
        operationId: 'getOrder',
        summary: 'Get a single order with items',
        tags: ['Orders'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Order detail',
                content: new OA\JsonContent(ref: '#/components/schemas/Order'),
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Not found'),
        ],
    )]
    public function show(Request $request, string $id): never
    {
        $auth  = Auth::requireAuth();
        $order = Order::findById((int) $id);

        if ($order === null) {
            Response::notFound('Order not found');
        }

        if ($auth['role'] !== 'admin' && $order->userId !== $auth['sub']) {
            Response::forbidden();
        }

        Response::success($this->toArray($order));
    }

    #[OA\Post(
        path: '/orders',
        operationId: 'createOrder',
        summary: 'Create an order from the current cart',
        tags: ['Orders'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Order created',
                content: new OA\JsonContent(ref: '#/components/schemas/Order'),
            ),
            new OA\Response(response: 400, description: 'Cart is empty'),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ],
    )]
    public function store(Request $request): never
    {
        $auth = Auth::requireAuth();

        try {
            $order = Order::createFromCart($auth['sub']);
        } catch (\RuntimeException $e) {
            Response::error($e->getMessage(), 400);
        }

        Response::success($this->toArray($order), 201);
    }

    #[OA\Put(
        path: '/orders/{id}/status',
        operationId: 'updateOrderStatus',
        summary: 'Update order status (admin only)',
        tags: ['Orders'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateOrderStatusRequest'),
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Updated order',
                content: new OA\JsonContent(ref: '#/components/schemas/Order'),
            ),
            new OA\Response(response: 400, description: 'Invalid status'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Not found'),
        ],
    )]
    public function updateStatus(Request $request, string $id): never
    {
        Auth::requireAdmin();

        $v = (new Validator($request->body))->required('status');

        if (!$v->passes()) {
            Response::error($v->firstError(), 400);
        }

        $status = (string) $request->body['status'];

        if (!in_array($status, self::VALID_STATUSES, true)) {
            Response::error(
                'status must be one of: ' . implode(', ', self::VALID_STATUSES),
                400,
            );
        }

        $order = Order::updateStatus((int) $id, $status);

        if ($order === null) {
            Response::notFound('Order not found');
        }

        Response::success($this->toArray($order));
    }

    /** @return array<string, mixed> */
    private function toArray(Order $o): array
    {
        return [
            'id'         => $o->id,
            'user_id'    => $o->userId,
            'status'     => $o->status,
            'total'      => $o->total,
            'items'      => array_map(fn (OrderItem $i) => $this->itemToArray($i), $o->items),
            'created_at' => $o->createdAt,
            'updated_at' => $o->updatedAt,
        ];
    }

    /** @return array<string, mixed> */
    private function itemToArray(OrderItem $i): array
    {
        return [
            'id'         => $i->id,
            'product_id' => $i->productId,
            'name'       => $i->name,
            'price'      => $i->price,
            'quantity'   => $i->quantity,
            'subtotal'   => $i->subtotal(),
        ];
    }
}
