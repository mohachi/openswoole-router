<?php

namespace Mohachi\Openswoole;

use OpenSwoole\Http\Server as HttpServer;

class Server extends HttpServer
{
    use Routing;
    
    #[\Override]
    public function start(): bool
    {
        $this->register($this);
        return parent::start();
    }
}
