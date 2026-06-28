<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Auth;
use App\Http\Request;
use App\Http\Response;
use App\Model\Cart;
use App\Model\CartItemRow;
use App\Validation\Validator;
use OpenApi\Attributes as OA;

final class CartController
{
    #[OA\Get(
        path: '/cart',
        operationId: 'getCart',
        summary: 'Get the authenticated user\'s cart',
        tags: ['Cart'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Current cart',
                content: new OA\JsonContent(ref: '#/components/schemas/Cart'),
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ],
    )]
    public function index(Request $request): never
    {
        $auth = $this->requireBuyer();
        $cart = Cart::getWithItems($auth['sub']);

        Response::success($this->cartToArray($cart));
    }

    #[OA\Post(
        path: '/cart/items',
        operationId: 'addCartItem',
        summary: 'Add an item to the cart',
        tags: ['Cart'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['product_id'],
                properties: [
                    new OA\Property(property: 'product_id', type: 'integer', example: 5),
                    new OA\Property(property: 'quantity',   type: 'integer', example: 1),
                ],
            ),
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Item added — returns updated cart',
                content: new OA\JsonContent(ref: '#/components/schemas/Cart'),
            ),
            new OA\Response(response: 400, description: 'Validation error'),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ],
    )]
    public function addItem(Request $request): never
    {
        $auth = $this->requireBuyer();

        $v = (new Validator($request->body))->required('product_id');

        if (!$v->passes()) {
            Response::error($v->firstError(), 400);
        }

        $productId = (int) $request->body['product_id'];
        $quantity  = max(1, (int) ($request->body['quantity'] ?? 1));

        $cart = Cart::addItem($auth['sub'], $productId, $quantity);

        Response::success($this->cartToArray($cart), 201);
    }

    #[OA\Put(
        path: '/cart/items/{id}',
        operationId: 'updateCartItem',
        summary: 'Update the quantity of a cart item',
        tags: ['Cart'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['quantity'],
                properties: [
                    new OA\Property(property: 'quantity', type: 'integer', minimum: 1, example: 3),
                ],
            ),
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Updated cart',
                content: new OA\JsonContent(ref: '#/components/schemas/Cart'),
            ),
            new OA\Response(response: 400, description: 'Validation error'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Item not found'),
        ],
    )]
    public function updateItem(Request $request, string $id): never
    {
        $auth = $this->requireBuyer();

        $v = (new Validator($request->body))->required('quantity');

        if (!$v->passes()) {
            Response::error($v->firstError(), 400);
        }

        $quantity = (int) $request->body['quantity'];

        if ($quantity < 1) {
            Response::error('quantity must be at least 1', 400);
        }

        try {
            $cart = Cart::setItemQuantity($auth['sub'], (int) $id, $quantity);
        } catch (\RuntimeException $e) {
            Response::notFound($e->getMessage());
        }

        Response::success($this->cartToArray($cart));
    }

    #[OA\Delete(
        path: '/cart/items/{id}',
        operationId: 'removeCartItem',
        summary: 'Remove an item from the cart',
        tags: ['Cart'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Updated cart after removal',
                content: new OA\JsonContent(ref: '#/components/schemas/Cart'),
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Item not found'),
        ],
    )]
    public function removeItem(Request $request, string $id): never
    {
        $auth = $this->requireBuyer();

        try {
            $cart = Cart::removeItem($auth['sub'], (int) $id);
        } catch (\RuntimeException $e) {
            Response::notFound($e->getMessage());
        }

        Response::success($this->cartToArray($cart));
    }

    #[OA\Delete(
        path: '/cart',
        operationId: 'clearCart',
        summary: 'Clear all items from the cart',
        tags: ['Cart'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Empty cart',
                content: new OA\JsonContent(ref: '#/components/schemas/Cart'),
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ],
    )]
    public function clear(Request $request): never
    {
        $auth = $this->requireBuyer();

        Cart::clearByUserId($auth['sub']);
        $cart = Cart::getWithItems($auth['sub']);

        Response::success($this->cartToArray($cart));
    }

    /** @return array{sub: int, email: string, role: string} */
    private function requireBuyer(): array
    {
        $auth = Auth::requireAuth();
        if ($auth['role'] === 'admin') {
            Response::forbidden('Penjual tidak dapat menggunakan keranjang belanja');
        }
        return $auth;
    }

    /** @return array<string, mixed> */
    private function cartToArray(Cart $c): array
    {
        return [
            'id'    => $c->id,
            'items' => array_map(fn (CartItemRow $i) => $this->itemToArray($i), $c->items),
            'total' => $c->total(),
        ];
    }

    /** @return array<string, mixed> */
    private function itemToArray(CartItemRow $i): array
    {
        return [
            'id'                => $i->id,
            'product_id'        => $i->productId,
            'product_name'      => $i->productName,
            'product_image_url' => $i->imageUrl(),
            'price'             => $i->price,
            'quantity'          => $i->quantity,
            'subtotal'          => $i->subtotal(),
        ];
    }
}
