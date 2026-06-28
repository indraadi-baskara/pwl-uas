<?php

declare(strict_types=1);

namespace App;

use OpenApi\Attributes as OA;

#[OA\Info(title: 'PWL UAS API', version: '1.0.0')]
#[OA\Server(url: 'http://localhost:8000', description: 'Local dev server')]
final class OpenApi {}
