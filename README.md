# Openswoole router

HTTP router for the Openswoole HTTP server.

## Requirement

- PHP [Openswoole](https://openswoole.com/docs) extension.

## Install

```shell
composer require mohachi/openswoole-router
```

## Usage

```php
<?php

use Mohachi\Openswoole\Router;
use OpenSwoole\Http\Request;
use OpenSwoole\Http\Response;
use OpenSwoole\Http\Server;

require_once __DIR__ . "/vendor/autoload.php";

$router = new Router();
$server = new Server("127.0.0.1", 9501);

// callback handler
$route->get("/*", function(Request $request)
{
    Response::create($request->fd)->end("welcome");
});

// direct file routing
$router->get("/", "public/index.html");

// direct directory routing
$router->get("/**", function(Request $request)
{
    $response = Response::create($request->fd);
    $path = __DIR__ . "/public" . $request->server["request_uri"];
    
    if( file_exists($path) )
    {
        $response->sendfile($path);
    }
});

$router->register($server);
$server->start();

```
****