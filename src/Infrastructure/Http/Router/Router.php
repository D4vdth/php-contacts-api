<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Router;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;

final class Router
{
    /** @var array<string, list<array{pattern: string, handler: callable}>> */
    private array $routes = [];


    public function get(string $pattern, callable $handler): void
    {
        $this->addRoute('GET', $pattern, $handler);
    }

    public function post(string $pattern, callable $handler): void
    {
        $this->addRoute('POST', $pattern, $handler);
    }

    public function put(string $pattern, callable $handler): void
    {
        $this->addRoute('PUT', $pattern, $handler);
    }

    public function delete(string $pattern, callable $handler): void
    {
        $this->addRoute('DELETE', $pattern, $handler);
    }

    public function dispatch(Request $request): Response
    {
        $method = $request->method();
        $path   = $request->path();

        $matchedRoute = $this->matchRoute($method, $path);

        if ($matchedRoute !== null) {
            [$handler, $params] = $matchedRoute;

            $enrichedRequest = $this->injectRouteParams($request, $params);

            return ($handler)($enrichedRequest);
        }

        if ($this->pathExistsForOtherMethod($method, $path)) {
            return Response::methodNotAllowed($method, $path);
        }

        return Response::notFound(
            sprintf('No route found for %s %s.', $method, $path)
        );
    }

    private function addRoute(string $method, string $pattern, callable $handler): void
    {
        $this->routes[$method][] = [
            'pattern' => $pattern,
            'handler' => $handler,
        ];
    }

    /**
     * @return array{callable, array<string, string>}|null
     */
    private function matchRoute(string $method, string $path): ?array
    {
        foreach ($this->routes[$method] ?? [] as $route) {
            $regex = $this->patternToRegex($route['pattern']);

            if (preg_match($regex, $path, $matches) === 1) {
                $params = array_filter(
                    $matches,
                    fn (string $key): bool => !is_int($key),
                    ARRAY_FILTER_USE_KEY,
                );

                return [$route['handler'], $params];
            }
        }

        return null;
    }

    private function patternToRegex(string $pattern): string
    {
        $regex = preg_replace('/{(\w+)}/', '(?P<$1>[^/]+)', $pattern);

        return '#^' . $regex . '$#';
    }

    /**
     * @param array<string, string> $params
     */
    private function injectRouteParams(Request $request, array $params): Request
    {
        foreach ($params as $key => $value) {
            $request = $request->withRouteParam($key, $value);
        }

        return $request;
    }

    private function pathExistsForOtherMethod(string $currentMethod, string $path): bool
    {
        foreach ($this->routes as $method => $routes) {
            if ($method === $currentMethod) {
                continue;
            }

            foreach ($routes as $route) {
                $regex = $this->patternToRegex($route['pattern']);

                if (preg_match($regex, $path) === 1) {
                    return true;
                }
            }
        }

        return false;
    }
}