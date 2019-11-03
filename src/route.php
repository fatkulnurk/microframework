<?php

use Fatkulnurk\Microframework\Routing\RouteCollector;

return function (RouteCollector $r) {

    /**
     * Example GET route
     *
     * @param  array                  $args Route parameters
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    $r->addRoute('GET', '/', function ($args) {
//        echo "hello";
//        echo "aaaaaaa";
        echo json_encode([
            'a' => 'b'
        ]);
        header('Content-Type: application/json');
    });

    $r->addRoute('GET', '/home', function ($args, $request, $response) {
        echo "aa";
    });

    $r->addRoute('GET', '/test/{name}', 'Coba::index');
};

// testing handler
class Coba {
    public function index() {
        echo "Aaaa";
    }
}
