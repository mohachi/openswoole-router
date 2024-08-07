<?php

use Mohachi\Openswoole\Server;
use OpenSwoole\Http\Request;
use OpenSwoole\Http\Response;

require_once __DIR__ . "/../../vendor/autoload.php";

$server = new Server("localhost", 1111);
$handler = function(Request $request)
{
    dump($request->server["request_uri"]);
    Response::create($request->fd)->end("welcome\n");
};

$server->get("/user/{id}", function(Request $request, $id)
{
    Response::create($request->fd)->end("id = $id\n");
});

$server->get("/", $handler);
$server->get("/**", $handler);

$server->start();
