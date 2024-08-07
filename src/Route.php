<?php

namespace Mohachi\Openswoole;

use Closure;
use Exception;
use OpenSwoole\Http\Request;
use OpenSwoole\Http\Response;

class Route
{
    
    private $arguments = [];
    private $subtitutions = [
        "~(/\\\\\*(\\\\\*)+)+~" => "(?:/.+?)?",
        "~\\\\\*~" => "[^/]+?",
        "~\\\\{([\da-z][\w_]*?)\\\\}~i" => "(?<$1>[^/]+?)"
    ];
    
    readonly string $pattern;
    readonly Closure $handler;
    
    public function __construct(
        string $pattern,
        Closure|string $handler
    )
    {
        $pattern = preg_replace(
            array_keys($this->subtitutions),
            array_values($this->subtitutions),
            preg_quote($pattern, "~")
        );
        
        if( null === $pattern )
        {
            throw new Exception("Invalid route pattern");
        }
        
        $this->pattern = "~^$pattern$~";
        
        if( is_string($handler) )
        {
            $path = realpath($handler);
            
            if( false == $path )
            {
                throw new Exception("\"$path\" file not found");
            }
            
            $handler = function(Request $request) use ($path)
            {
                Response::create($request->fd)->sendfile($path);
            };
        }
        
        $this->handler = $handler;
    }
    
    public function match(string $path): bool
    {
        $result = preg_match($this->pattern, $path, $matches);
        $this->arguments = array_filter($matches, fn($k) => is_string($k), ARRAY_FILTER_USE_KEY);
        return $result;
    }
    
    public function getArguments()
    {
        return $this->arguments;
    }
}

