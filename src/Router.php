<?php

namespace Mohachi\Router;

use Closure;
use Mohachi\Router\HTTP\Method;
use OpenSwoole\Http\Request;
use OpenSwoole\Http\Response;
use OpenSwoole\Http\Server;

class Router
{
    private $routes = [
        Method::Get->value => [],
        Method::Post->value => [],
    ];
    
    public function get(string $pattern, Closure|string $handler)
    {
        $this->routes[Method::Get->value][] = new Route($pattern, $handler);
    }
    
    public function post(string $pattern, Closure|string $handler)
    {
        $this->routes[Method::Post->value][] = new Route($pattern, $handler);
    }
    
    public function any(string $pattern, Closure|string $handler)
    {
        $route = new Route($pattern, $handler);
        $this->routes[Method::Get->value][] = $route;
        $this->routes[Method::Post->value][] = $route;
    }
    
    public function match(Request $request): ?Route
    {
        $path = $request->server["path_info"];
        
        foreach( $this->routes[$request->getMethod()] as $route )
        {
            if( $route->match($path) )
            {
                return $route;
            }
        }
        
        return null;
    }
    
    public function register(Server $server)
    {
        $server->on("request", function(Request $request, Response $response)
        {
            $response->detach();
            $route = $this->match($request);
            
            if( null !== $route )
            {
                $args = [$request, ...array_values($route->getArguments())];
                call_user_func_array($route->handler, $args);
            }
        });
    }
}

