<?php

declare(strict_types=1);

namespace App\Http;

final class Router
{
    /** @var array<string, array<string, array{class: class-string, method: string}>> */
    private array $routes = [];

    /** @param class-string $controller */
    public function get(string $path, string $controller, string $method): self
    {
        return $this->addRoute('GET', $path, $controller, $method);
    }

    /** @param class-string $controller */
    public function post(string $path, string $controller, string $method): self
    {
        return $this->addRoute('POST', $path, $controller, $method);
    }

    /** @param class-string $controller */
    public function put(string $path, string $controller, string $method): self
    {
        return $this->addRoute('PUT', $path, $controller, $method);
    }

    /** @param class-string $controller */
    public function delete(string $path, string $controller, string $method): self
    {
        return $this->addRoute('DELETE', $path, $controller, $method);
    }

    /** @param class-string $controller */
    private function addRoute(string $httpMethod, string $path, string $controller, string $action): self
    {
        $this->routes[$httpMethod][$path] = ['class' => $controller, 'method' => $action];
        return $this;
    }

    public function dispatch(Request $request): void
    {
        $methodRoutes = $this->routes[$request->method] ?? null;

        if ($methodRoutes === null) {
            Response::methodNotAllowed();
        }

        foreach ($methodRoutes as $pattern => $handler) {
            $params = $this->match($pattern, $request->path);

            if ($params === null) {
                continue;
            }

            $instance = new $handler['class']();
            $action   = $handler['method'];
            $instance->$action($request, ...$params);
            return;
        }

        // Check if the path exists under a different method (405 vs 404).
        foreach ($this->routes as $method => $routes) {
            if ($method === $request->method) {
                continue;
            }

            foreach (array_keys($routes) as $pattern) {
                if ($this->match($pattern, $request->path) !== null) {
                    Response::methodNotAllowed();
                }
            }
        }

        Response::notFound();
    }

    /**
     * Converts `/users/{id}` to a regex and returns captured params, or null on no match.
     *
     * @return array<int, string>|null
     */
    private function match(string $pattern, string $path): array|null
    {
        $regex = preg_replace('/\{[^}]+\}/', '([^/]+)', $pattern);

        if ($regex === null) {
            return null;
        }

        $regex = '#^' . $regex . '$#';

        if (preg_match($regex, $path, $matches) !== 1) {
            return null;
        }

        array_shift($matches);

        return $matches;
    }
}
