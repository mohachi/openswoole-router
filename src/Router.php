<?php

namespace Mohachi\Router;

use Exception;
use Mohachi\Router\HTTP\Request as HTTPRequest;
use OpenSwoole\Http\Request;
use OpenSwoole\Http\Response;
use OpenSwoole\Http\Server;
use OutOfBoundsException;
use ReflectionFunction;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionUnionType;

class Router
{
    const GET = "GET";
    const POST = "POST";
    
    private $subtitutions = [
        "~(/\\\\\*(\\\\\*)+)+~" => "(?:/.+?)?",
        "~\\\\\*~" => "[^/]+?",
        "~\\\\{([\da-z][\w_]*?)\\\\}~i" => "(?<$1>[^/]+?)"
    ];
    
    // [ ["GET" => ["pattern" => "", "handler" => func] ]... ]
    private $handlers = [];
    
    public function __construct(readonly Server $server) {}
    
    public function post(string $pattern, callable $handler)
    {
        $this->setHandler(self::POST, $pattern, $handler);
    }
    
    public function get(string $pattern, callable $handler)
    {
        $this->setHandler(self::GET, $pattern, $handler);
    }
    
    public function setHandler(string $method, string $pattern, callable $handler)
    {
        $p = preg_replace(
            array_keys($this->subtitutions),
            array_values($this->subtitutions),
            preg_quote($pattern, "~")
        );
        
        if( null === $p )
        {
            throw new Exception("Unvalid route pattern");
        }
        
        $this->handlers[$method][] = [
            "pattern" => "~^$p$~",
            "handler" => $handler,
            "params" => (new ReflectionFunction($handler))->getParameters()
        ];
    }
    
    public function register()
    {
        $this->server->on("request", function(Request $request, Response $response)
        {
            $hash = [];
            $path = $request->server["path_info"];
            
            foreach( $this->handlers[$request->getMethod()] as [
                "pattern" => $pattern,
                "handler" => $handler,
                "params" => $params] )
            {
                if( preg_match($pattern, $path, $matches) )
                {
                    $args = [];
                    $hash += array_filter($matches, fn($i) => is_string($i), ARRAY_FILTER_USE_KEY);
                    $routeRequest = new HTTPRequest($request, $hash);
                    
                    foreach( $params as $param )
                    {
                        $types = [];
                        if( ! $param->hasType() && key_exists($param->name, $hash) )
                        {
                            $args[] = $hash[$param->name];
                            continue;
                        }
                        elseif( $param->getType() instanceof ReflectionUnionType || $param->getType() instanceof ReflectionIntersectionType )
                        {
                            $types = $param->getType()->getTypes();
                        }
                        elseif( $param->getType() instanceof ReflectionNamedType )
                        {
                            $types[] = $param->getType()->getName();
                        }
                        
                        foreach( $types as $type )
                        {
                            $args[] = match($type)
                            {
                                Request::class => $request,
                                Response::class => $response,
                                HTTPRequest::class => $routeRequest,
                                default => throw new OutOfBoundsException()
                            };
                        }
                        
                    }
                    
                    call_user_func_array($handler, $args);
                    
                    if( $routeRequest->isEnded() )
                    {
                        return;
                    }
                }
            }
            
            if( $response->isWritable() )
            {
                $response->status(404);
            }
        });
    }
    
}
