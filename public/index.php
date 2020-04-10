<?php
require_once '../vendor/autoload.php';

use Fatkulnurk\Microframework\App;

// membuat instance app
$app = App::getInstance();
$app->setPath(__DIR__);

// pendaftaran routes
$routes = require __DIR__ . '/../src/route.php';
$app->routing($routes);

// menjalankan custom errornya, saya pakai whoops
$app->errorView();

// menjalankan aplikasi & proses dispatch
$app->dispatch();
