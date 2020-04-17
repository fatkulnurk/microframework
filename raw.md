
PHP Type Hinting Mixed
https://wiki.php.net/rfc/mixed-typehint


```` 


                /*
                 * Pada bagian ini di cek, apalah $handler itu callable atau callback atau tidak.
                 * Jika tidak, maka dilakukan explode dengan delimeter /
                 * untuk mendapatkan informasi class dan method, lalu menjalankan.
                 *
                 * Dokumentasi selengkapnya: https://www.php.net/manual/en/language.types.callable.php
                 * */
//                if (is_callable($handler, true)) {
//                    call_user_func($handler, $vars);
//                } else {
//                    list($class, $method) = explode("/", $handler, 2);
//                    call_user_func_array(array(new $class, $method), $vars);
//                }

                if (is_callable($handler, true)) {
                    $response = call_user_func($handler, $vars);
                } else {
                    list($class, $method) = explode("/", $handler, 2);
                    $response = call_user_func_array(array(new $class, $method), $vars);
                }


//                if ($response instanceof \Nyholm\Psr7\Response) {
                if ($response instanceof Response) {
                    if ($response->hasHeader('location')) {
                        $result = $response->getHeader('location');
                        header("Location: ". (string) $result[0], true, 301);
                        die();
                    }


                    (new \Zend\HttpHandlerRunner\Emitter\SapiEmitter())->emit($response);
//                    $emitter = new SapiEmitter();
//                    $emitter->emit($response);
                } else {
                    if (is_callable($handler, true)) {
                        // enggak usah di pakai, kan udah di panggil diatas
//                        call_user_func($handler, $vars);
                    } else {
                        list($class, $method) = explode("/", $handler, 2);
                        call_user_func_array(array(new $class, $method), $vars);
                    }
                }
````