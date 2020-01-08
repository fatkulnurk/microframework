<?php
use Fatkulnurk\Microframework\Http\Message\Response;
use Fatkulnurk\Microframework\Routing\RouteCollector;

return function (RouteCollector $r) {
    $r->addRoute('GET', '/', function ($args) {
        echo "Hello World";
    });
};