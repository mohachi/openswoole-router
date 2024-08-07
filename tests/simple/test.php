<?php

use Mohachi\Router\Router;
use OpenSwoole\Http\Request;
use OpenSwoole\Http\Response;
use OpenSwoole\Http\Server;

define("PUBLIC_DIR", __DIR__ . "/public");

require_once __DIR__ . "/../../vendor/autoload.php";

$router = new Router();
$server = new Server("localhost", 1111);

$router->get("/", PUBLIC_DIR . "/index.html");
$router->get("/login", __DIR__ . "/view/login.html");

$router->get("/**", function(Request $request)
{
    $response = Response::create($request->fd);
    $path = PUBLIC_DIR . "{$request->server["path_info"]}";
    
    if( file_exists($path) )
    {
        $response->sendfile($path);
    }
});

$router->register($server);
$server->start();
