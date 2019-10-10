<?php
use Mifa\Routing\RouteCollector;

return function (RouteCollector $r) {
    $r->addRoute('GET', '/', function (){
    });
};