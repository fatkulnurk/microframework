<?php
require_once 'vendor/autoload.php';

use Mifa\App;

// membuat instance app
$app = App::getInstance();

// pendaftaran routes
$routes = require __DIR__ . '/../src/route.php';
$app->routing($routes);

// menjalankan aplikasi & proses dispatch
$app->dispatch();
