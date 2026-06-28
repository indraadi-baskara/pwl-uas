<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Controller\AuthController;
use App\Controller\HealthController;
use App\Http\Request;
use App\Http\Router;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
$dotenv->load();

$origin = getenv('FRONTEND_URL') ?: 'http://localhost:5173';

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

$router->dispatch($request);
