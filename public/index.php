<?php
require_once '../vendor/autoload.php';

use Fatkulnurk\Microframework\App;

// create instance
$app = App::getInstance();

// set Base Dir
$app->setPath(__DIR__);

// set config
$app->setConfig([
    'path_template' => __DIR__ . "./../src/views",
    'path_public' => __DIR__ . "./../public/"
]);

// registration route
$routes = require __DIR__ . '/../src/route.php';
$app->routing($routes);

// error handling (with whoops)
$app->errorView();

// run app
$app->dispatch();
