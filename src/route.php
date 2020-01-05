<?php

use Fatkulnurk\Microframework\Http\Message\Response;
use Fatkulnurk\Microframework\Routing\RouteCollector;

return function (RouteCollector $r) {
    $r->addRoute('GET', '/', function ($args) {

        var_dump(\Fatkulnurk\Microframework\Http\Message\ServerRequest::getInstance()
            ->make('GET', $_SERVER['REQUEST_URI'])->getUri());

        die();
        return Response::getInstance()
            ->withView('index', [
                'name' => 'fatkul nur koirudin',
                'birthday' => '18 januari 1999'
                ], new \Fatkulnurk\Microframework\Http\Data\View\Page());

//       /* Contoh return data view tanpa template factory */
//        return Response::getInstance()
//            ->withView('index', [
//                'name' => 'fatkul nur k',
//                'birthday' => '18 januari 1999'
//            , new \Fatkulnurk\Microframework\Http\Data\View\LexTemplateFactory()]);
    });

    $r->addRoute('GET', '/tes-json', function ($args) {
        $data = [
            'biodata' => [
                'nama' => 'fatkul nur koirudin',
                'ttl' => 'Lamongan, 18 Januari 1999',
                'alamat' => [
                    'desa' => 'Desa Ngambeg',
                    'kecamatan' => 'Kecamatan Pucuk',
                    'kabupaten' => 'Kabupaten Lamongan'
                ],
                'email' => 'fatkulnurk@gmail.com',
                'hoby' => [
                    'memancing',
                    'belajar hal baru'
                ]
            ]
        ];

        return Response::getInstance()
            ->withJson($data);
    });

    $r->addRoute('GET', '/tes-redirect', function ($args) {
        return Response::getInstance()
            ->withRedirect('http://google.com');
    });

    $r->addRoute('GET', '/biodata', function ($args) {
        echo json_encode([
            'biodata' => [
                'nama' => 'fatkul nur koirudin',
                'ttl' => 'Lamongan, 18 Januari 1999',
                'alamat' => [
                    'desa' => 'Desa Ngambeg',
                    'kecamatan' => 'Kecamatan Pucuk',
                    'kabupaten' => 'Kabupaten Lamongan'
                ],
                'email' => 'fatkulnurk@gmail.com',
                'hoby' => [
                    'memancing',
                    'belajar hal baru'
                ]
            ]
        ]);
        header('Content-Type: application/json');
    });

    $r->addRoute('GET', '/home', function ($args) {
        echo "aa";
    });

    $r->addRoute('GET', '/user/{id:\d+}[/{name}]', function ($args) {
        var_dump($args);
    });

    $r->addRoute('GET', '/test/{name}', 'Coba::index');
};

// testing handler
class Coba
{
    public function index()
    {
        echo "Aaaa";
    }
}
