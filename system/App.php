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

    /** @return App */
    public static function getInstance() : App
    {
        if (self::$instance == null) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * Method ini untuk interaksi dengan routing
     * Pada bagian ini terdapat parser, data degerator, dispatcher dan routecollector
     * @param callable $routeDefinitionCallback
     * @param array $options
     * @return Dispatcher
     */
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

    /**
     * Method ini digunakan untuk melakukan pengecekan
     * Apakah alamat yang dikunjungi terdaftar atau tidak pada routing
     * Serta untuk pemanggilan handler jika memang alamat terdaftar
     *
     * Cara Kerjanya
     * Dicek apakah url terdaftar di routecollection, alias tempat routing di daftarkan
     *
     * Jika Tidak ditemukan
     * maka akan menjalankan Dispatcher bagian not found
     *
     * Jika ternyata alamat terdaftar, tetapi method pemanggilan salah
     * Maka, akan dijalankan dispatcher bagian method not allowed
     * Contohnya adalah
     *      Halaman localhost/about methodnya adalah GET
     *      Tetapi di akses dengan method POST
     *
     * Jika ternyata alamat terdaftar & Method pemanggilan benar
     * Maka, Akan menjalankan dispartcher bagian found
     *
     * @return mixed|void
     */
    public function dispatch() : void
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
                new \ErrorException("ERROR", 404);
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                // ... 405 Method Not Allowed
                new \ErrorException('Method Not Allow', '403');
                break;
            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];

                /*
                 * Pada bagian ini di cek, apalah $handler itu callable atau callback atau tidak.
                 * Jika tidak, maka dilakukan explode dengan delimeter /
                 * untuk mendapatkan informasi class dan method, lalu menjalankan.
                 *
                 * Dokumentasi selengkapnya: https://www.php.net/manual/en/language.types.callable.php
                 * */
                if (is_callable($handler, true)) {
                    call_user_func($handler, $vars);
                } else {
                    list($class, $method) = explode("/", $handler, 2);
                    call_user_func_array(array(new $class, $method), $vars);
                }

                break;
        }
    }
}
