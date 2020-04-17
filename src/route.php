<?php
use Fatkulnurk\Microframework\Http\Message\Response;
use Fatkulnurk\Microframework\Routing\RouteCollector;

return function (RouteCollector $r) {
    $r->addRoute(['GET', 'POST'], '/', function ($args) {
        return Response::getInstance()
            ->withView('index.twig', [
                'title' => 'Microframework',
                'message' => 'Welcome To Microframework',
            ]);
    });

    $r->addRoute('GET', '/name/{name}', function ($args) {
        echo "halo " . $args['name'];
    });

    $r->get('/welcome', 'App\Controllers\HomeController::welcome');
};