<?php

use Mohachi\Router\HTTP\Request;
use Mohachi\Router\Router;
use NunoMaduro\Collision\Provider;
use OpenSwoole\Http\Response;
use OpenSwoole\Http\Server;

define("PUBLIC_DIR", __DIR__ . "/public");

require_once __DIR__ . "/vendor/autoload.php";

(new Provider)->register();

$ser = new Server("localhost", 8888);
$router = new Router($ser);

// $router->get("/**", function(Request $request)
// {
//     dump($request->server["path_info"]);
//     $request->pass();
// });

// $router->get("/login", function(Response $response)
// {
//     $response->sendfile(__DIR__ . "/view/login.html");
// });

// $router->get("/**/chapter/{chapter}", function(Request $request)
// {
//     $request->pass();
// });

// $router->get("/manga/{manga}/**", function($manga, $chapter, Response $response, Request $request)
// {
//     $response->write("title = $manga<br>");
//     $response->write("chapter = $chapter");
// });

// $router->get("/book/{book}/**", function($book, $chapter, Response $response, Request $request)
// {
//     $response->write("title = $book<br>");
//     $response->write("chapter = $chapter");
// });

$router->get("/manga/{manga}/chapter/{n}", function(Response $response, $manga, $n)
{
    $response->write("title = $manga, chapter = $n");
});

$router->get("/**", function(Request $req, Response $response)
{
    $path = PUBLIC_DIR . "{$req->server["path_info"]}";
    if( file_exists($path) )
    {
        $response->sendfile($path);
    }
});

$router->register();
$ser->start();
