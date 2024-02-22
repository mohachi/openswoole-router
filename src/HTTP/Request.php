<?php

namespace Mohachi\Router\HTTP;

use BadMethodCallException;
use OpenSwoole\Http\Request as HttpRequest;
use OutOfBoundsException;

/**
 * @mixin HTTPRequest
 */
class Request
{
    
    private bool $end = true;
    
    /**
     * @param array<string, string> $data
     */
    public function __construct(readonly HttpRequest $request, private array $data) {}
    
    public function __get($key)
    {
        if( property_exists($this->request, $key) )
        {
            return $this->request->{$key};
        }
        
        if( key_exists($key, $this->data) )
        {
            return $this->data[$key];
        }
        
        throw new OutOfBoundsException();
    }
    
    public function __set($attr, $value)
    {
        if( ! property_exists($this->request, $attr) )
        {
            throw new OutOfBoundsException();
        }
        
        $this->request->{$attr} = $value;
    }
    
    public function __call($name, $arguments)
    {
        if( ! method_exists($this->request, $name) )
        {
            throw new BadMethodCallException();
        }
        
        return $this->request->$name($arguments);
        // return call_user_func_array([$this->request, $name], $arguments);
    }
    
    public function pass()
    {
        $this->end = false;
    }
    
    public function isEnded(): bool
    {
        return $this->end;
    }
    
}
