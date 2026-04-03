<?php

namespace App\Application\Routing;

use ReflectionClass;
use RuntimeException;

class Router
{
    private array $routes = [];

    public function registerController(object $controller): void
    {
        $reflection = new ReflectionClass($controller);

        foreach ($reflection->getMethods() as $method) {
            $attributes = $method->getAttributes(Route::class);

            foreach ($attributes as $attribute) {
                /** @var Route $route */
                $route = $attribute->newInstance();
                $this->routes[$route->method . '|' . $route->path] = [
                    'controller' => $controller,
                    'method' => $method->getName(),
                ];
            }
        }
    }

    public function dispatch(string $method, string $uri): void
    {
        $key = $method . '|' . $uri;

        header('Content-Type: application/json');

        if (isset($this->routes[$key])) {
            $handler = $this->routes[$key];

            try {
                // Execute the controller method
                $handler['controller']->{$handler['method']}();
            } catch (RuntimeException $e) {
                // Domain logic exceptions return 400 Bad Request
                http_response_code(400);
                echo json_encode([
                    'error' => 'Domain Rule Violation',
                    'message' => $e->getMessage(),
                ]);
            } catch (\Throwable $e) {
                // Unexpected system crashes return 500
                http_response_code(500);
                echo json_encode([
                    'error' => 'Internal Server Error',
                    'message' => 'An unexpected error occurred.',
                ]);
            }
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint not found']);
        }
    }
}
