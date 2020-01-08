# MICROFRAMEWORK
[![StyleCI](https://github.styleci.io/repos/210505965/shield?branch=master)](https://github.styleci.io/repos/210505965)
[![Build Status](https://travis-ci.org/fatkulnurk/microframework.svg?branch=master)](https://travis-ci.org/fatkulnurk/microframework)
[![CodeFactor](https://www.codefactor.io/repository/github/fatkulnurk/microframework/badge)](https://www.codefactor.io/repository/github/fatkulnurk/microframework)
[![Latest Stable Version](https://poser.pugx.org/fatkulnurk/microframework/v/stable)](https://packagist.org/packages/fatkulnurk/microframework)
[![Total Downloads](https://poser.pugx.org/fatkulnurk/microframework/downloads)](https://packagist.org/packages/fatkulnurk/microframework)
[![Latest Unstable Version](https://poser.pugx.org/fatkulnurk/microframework/v/unstable)](https://packagist.org/packages/fatkulnurk/microframework)

PHP Microframework

Baca dokumentasi selengkapnya [link belum ada].

---

## Cara Install
gunakan composer, lalu jalankan perintah dibawah ini (pastikan sudah install composer)

````
composer create-project --prefer-dist fatkulnurk/microframework nama_aplikasi
````

---
**Mendaftarkan Routing**

Pendaftaran Routing

contoh pendaftaran routing beserta implementasinya, contoh dibawah ini untuk return berupa xml.

````
use Fatkulnurk\Microframework\Routing\RouteCollector;

return function (RouteCollector $r) {
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
            ->withXml($data);
    });
};

````

Routing Handler Berupa Callback

dibawah ini contoh routing dengan callback.
````
    $r->addRoute('GET', '/', function ($args) {
        return Response::getInstance()
            ->withView('index', [
                'name' => 'fatkul nur k',
                'birthday' => '18 januari 1999'
            ]);
    });
````



Routing Dengan handler berupa class

dibawah ini contoh routing dengan handler berupa method dari suatu class.
perlu di ingat, untuk pemanggilan adalah NamaClass::NamaMethod
````
    $r->addRoute('GET', '/test/{name}', 'Coba::index');
````

Buat class sebagai handlernya, contohnya dibawah ini.
````
class Coba {
    public function index()
    {
        echo "Hello World";
    }
}
````

## Response
method yang bisa digunakan untuk response sama dengan yang ada pada aturan PSR 7.

untuk method tambahan adalah sebagai berikut:
- withView()
- withJson()
- withXml()
- withRedirect()
- withDownload()

## Request
semua sama seperti PSR 7. Gunakan seperti di framework lain.

---
**Cek CodeStyle**

Standard yang digunakan adalah PSR2, untuk menjalankan ketikan command berikut.

``
phpcs ./system --standard=psr2
``

--- 
**DocBlock**
Jalankan 
`u`

---
**Tool**
- PHP_CodeSniffer (phpcs & phpcbf), 
cara integrasi dengan phpstorm --> https://www.jetbrains.com/help/phpstorm/using-php-code-sniffer.html
- styleci
- travis-ci
- Github - Repository kode