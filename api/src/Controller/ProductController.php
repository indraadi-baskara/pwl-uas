<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Auth;
use App\Http\Request;
use App\Http\Response;
use App\Model\Product;
use App\Validation\Validator;
use OpenApi\Attributes as OA;

final class ProductController
{
    #[OA\Get(
        path: '/products',
        operationId: 'listProducts',
        summary: 'List products with pagination and filters',
        parameters: [
            new OA\Parameter(name: 'page',      in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'limit',     in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'search',    in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'category',  in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'min_price', in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'max_price', in: 'query', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [new OA\Response(response: 200, description: 'Product list')],
    )]
    public function index(Request $request): never
    {
        $page     = max(1, (int) ($request->query['page'] ?? 1));
        $limit    = min(100, max(1, (int) ($request->query['limit'] ?? 10)));
        $search   = (string) ($request->query['search'] ?? '');
        $category = (string) ($request->query['category'] ?? '');
        $minPrice = isset($request->query['min_price']) ? (int) $request->query['min_price'] : null;
        $maxPrice = isset($request->query['max_price']) ? (int) $request->query['max_price'] : null;

        $result = Product::findAll($page, $limit, $search, $category, $minPrice, $maxPrice);

        $data = array_map(fn (Product $p) => $this->toArray($p), $result['data']);

        Response::paginated($data, [
            'total'        => $result['total'],
            'per_page'     => $limit,
            'current_page' => $page,
            'last_page'    => (int) ceil($result['total'] / $limit),
        ]);
    }

    #[OA\Get(
        path: '/products/{id}',
        operationId: 'getProduct',
        summary: 'Get a single product',
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Product', content: new OA\JsonContent(ref: '#/components/schemas/Product')),
            new OA\Response(response: 404, description: 'Not found'),
        ],
    )]
    public function show(Request $request, string $id): never
    {
        $product = Product::findById((int) $id);

        if ($product === null) {
            Response::notFound('Product not found');
        }

        Response::success($this->toArray($product));
    }

    #[OA\Post(
        path: '/products',
        operationId: 'createProduct',
        summary: 'Create a product (admin only)',
        responses: [
            new OA\Response(response: 201, description: 'Created', content: new OA\JsonContent(ref: '#/components/schemas/Product')),
            new OA\Response(response: 400, description: 'Validation error'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden'),
        ],
    )]
    public function store(Request $request): never
    {
        Auth::requireAdmin();

        $body = $this->parseMultipart($request);

        $v = (new Validator($body))
            ->required('name')
            ->required('price');

        if (!$v->passes()) {
            Response::error($v->firstError(), 400);
        }

        $imagePath = $this->handleUpload();

        $product = Product::create([
            'name'        => $body['name'],
            'description' => $body['description'] ?? null,
            'price'       => (int) $body['price'],
            'stock'       => (int) ($body['stock'] ?? 0),
            'category'    => $body['category'] ?? null,
            'image_path'  => $imagePath,
        ]);

        Response::success($this->toArray($product), 201);
    }

    #[OA\Put(
        path: '/products/{id}',
        operationId: 'updateProduct',
        summary: 'Update a product (admin only)',
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Updated', content: new OA\JsonContent(ref: '#/components/schemas/Product')),
            new OA\Response(response: 404, description: 'Not found'),
        ],
    )]
    public function update(Request $request, string $id): never
    {
        Auth::requireAdmin();

        $product = Product::findById((int) $id);

        if ($product === null) {
            Response::notFound('Product not found');
        }

        $body      = $this->parseMultipart($request);
        $imagePath = $this->handleUpload() ?? $product->imagePath;

        $data = array_filter([
            'name'        => $body['name'] ?? null,
            'description' => $body['description'] ?? null,
            'price'       => isset($body['price']) ? (int) $body['price'] : null,
            'stock'       => isset($body['stock']) ? (int) $body['stock'] : null,
            'category'    => $body['category'] ?? null,
            'image_path'  => $imagePath,
        ], static fn ($v) => $v !== null);

        $updated = Product::update((int) $id, $data);

        Response::success($this->toArray($updated ?? $product));
    }

    #[OA\Delete(
        path: '/products/{id}',
        operationId: 'deleteProduct',
        summary: 'Delete a product (admin only)',
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Deleted'),
            new OA\Response(response: 404, description: 'Not found'),
        ],
    )]
    public function destroy(Request $request, string $id): never
    {
        Auth::requireAdmin();

        if (!Product::delete((int) $id)) {
            Response::notFound('Product not found');
        }

        Response::success(['message' => 'Product deleted']);
    }

    /** @return array<string, mixed> */
    private function toArray(Product $p): array
    {
        return [
            'id'          => $p->id,
            'name'        => $p->name,
            'description' => $p->description,
            'price'       => $p->price,
            'stock'       => $p->stock,
            'category'    => $p->category,
            'image_url'   => $p->imageUrl(),
            'created_at'  => $p->createdAt,
        ];
    }

    /** @return array<string, mixed> */
    private function parseMultipart(Request $request): array
    {
        // For multipart/form-data, PHP populates $_POST. Fall back to JSON body.
        return $_POST !== [] ? $_POST : $request->body;
    }

    private function handleUpload(): ?string
    {
        $file = $_FILES['image'] ?? null;

        if ($file === null || (int) $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $allowed   = ['image/jpeg', 'image/png', 'image/webp'];
        $mimeType  = mime_content_type($file['tmp_name']);

        if ($mimeType === false || !in_array($mimeType, $allowed, true)) {
            Response::error('Only JPEG, PNG, and WebP images are allowed', 400);
        }

        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = bin2hex(random_bytes(16)) . '.' . strtolower($ext);
        $dest     = __DIR__ . '/../../public/uploads/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            Response::error('Failed to save uploaded file', 500);
        }

        return $filename;
    }
}
