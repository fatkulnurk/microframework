<?php

declare(strict_types=1);

namespace Mifa;

use Mifa\Routing\Dispatcher;
use Mifa\Routing\RouteParser;
use Mifa\Routing\DataGenerator;
use Mifa\Routing\RouteCollector;

class App
{
    /** @var App|null Untuk menyimpan instance dari class ini */
    private static $instance = null;

    /** @var Dispatcher|null Untuk menyimpan informasi dari routing, setelah di deklarasikan */
    private $dispatcher;

    /** @var RequestInterface|null Untuk menyimpan informasi dari request secara globals */
    private $request = null;

    /** @var ResponseInterface|null Untuk menyimpan informasi dari response secara globals */
    private $response = null;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public function routing(callable $routeDefinitionCallback, array $options = []): Dispatcher
    {
        $options += [
            'routeParser' => RouteParser\Std::class,
            'dataGenerator' => DataGenerator\GroupCountBased::class,
            'dispatcher' => Dispatcher\GroupCountBased::class,
            'routeCollector' => RouteCollector::class,
        ];

        /** @var RouteCollector $routeCollector */
        $routeCollector = new $options['routeCollector'](
            new $options['routeParser'](), new $options['dataGenerator']()
        );
        $routeDefinitionCallback($routeCollector);

        $this->dispatcher = new $options['dispatcher']($routeCollector->getData());

        return new $options['dispatcher']($routeCollector->getData());
//        $this->dispatcher = new $options['dispatcher']($routeCollector->getData());
    }

    /*
     *
     * @return void
     * */
    public function dispatch()
    {
        // Fetch method and URI from somewhere
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

        // Strip query string (?foo=bar) and decode URI
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        $routeInfo = $this->dispatcher->dispatch($httpMethod, $uri);

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                // ... 404 Not Found
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                // ... 405 Method Not Allowed
            new \ErrorException('Method Not Allow', '403');
                break;
            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
                // ... call $handler with $vars
                die('yeah');
                break;
        }
    }
}
