<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Controller\AuthController;
use App\Controller\CartController;
use App\Controller\HealthController;
use App\Controller\OrderController;
use App\Controller\ProductController;
use App\Http\Request;
use App\Http\Router;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
$dotenv->load();

$origin = $_ENV['FRONTEND_URL'] ?? getenv('FRONTEND_URL') ?: 'http://localhost:5173';

header("Access-Control-Allow-Origin: {$origin}");
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$request = new Request();
$router  = new Router();

$router->get('/health', HealthController::class, 'index');

$router->get('/auth/me',        AuthController::class, 'me');
$router->post('/auth/register', AuthController::class, 'register');
$router->post('/auth/login',    AuthController::class, 'login');
$router->post('/auth/refresh',  AuthController::class, 'refresh');
$router->post('/auth/logout',   AuthController::class, 'logout');

$router->get('/products',          ProductController::class, 'index');
$router->get('/products/{id}',     ProductController::class, 'show');
$router->post('/products',         ProductController::class, 'store');
$router->put('/products/{id}',     ProductController::class, 'update');
$router->delete('/products/{id}',  ProductController::class, 'destroy');

$router->get('/cart',                CartController::class, 'index');
$router->post('/cart/items',         CartController::class, 'addItem');
$router->put('/cart/items/{id}',     CartController::class, 'updateItem');
$router->delete('/cart/items/{id}',  CartController::class, 'removeItem');
$router->delete('/cart',             CartController::class, 'clear');

$router->get('/orders',              OrderController::class, 'index');
$router->get('/orders/{id}',         OrderController::class, 'show');
$router->post('/orders',             OrderController::class, 'store');
$router->put('/orders/{id}/status',  OrderController::class, 'updateStatus');

$router->dispatch($request);
