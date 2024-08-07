<?php

use Mohachi\Router\Router;
use OpenSwoole\Http\Request;
use OpenSwoole\Http\Response;
use OpenSwoole\Http\Server;

require_once __DIR__ . "/../../vendor/autoload.php";

$ser = new Server("localhost", 1111);
$router = new Router();

$router->get("/manga/{manga}/chapter/{chapter}", function(Request $request, $manga, $chapter)
{
    dump($request, $manga, $chapter);
    $res = Response::create($request->fd);
    $res->end("welcome\n");
});

$router->register($ser);
$ser->start();
