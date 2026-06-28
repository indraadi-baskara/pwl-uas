<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Request;
use App\Http\Response;

final class HealthController
{
    public function index(Request $request): never
    {
        Response::json(['status' => 'ok']);
    }
}
