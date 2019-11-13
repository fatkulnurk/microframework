<?php
include 'vendor\autoload.php';

use Mifa\Http\Message\Uri;

$uri = new Uri('https://user:pass@example.com:8080/path/123?q=abc#test');
//echo $uri->getPath();
//echo $uri->getQuery();
echo $uri->getQuery();