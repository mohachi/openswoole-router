<?php

use Mohachi\Openswoole\Router;
use OpenSwoole\Http\Request;
use OpenSwoole\Http\Response;
use OpenSwoole\Http\Server;

require_once __DIR__ . "/../../vendor/autoload.php";

$router = new Router();
$server = new Server("localhost", 1111);

$router->get("/manga/{manga}/chapter/{chapter}", function(Request $request, $manga, $chapter)
{
    dump($request, $manga, $chapter);
    $res = Response::create($request->fd);
    $res->end("welcome\n");
});

$router->register($server);
$server->start();
