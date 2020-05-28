<?php


namespace Fatkulnurk\Microframework\Http\Message;

use Adbar\Dot;
use Fatkulnurk\Microframework\Core\Singleton;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;

final class ServerRequest implements ServerRequestInterface
{
    use MessageTrait;
    use RequestTrait;
    use Singleton;

    /** @var array */
    private $attributes = [];

    /** @var array */
    private $cookieParams = [];

    /** @var array|object|null */
    private $parsedBody;

    /** @var array */
    private $queryParams = [];

    /** @var array */
    private $serverParams;

    /** @var UploadedFileInterface[] */
    private $uploadedFiles = [];

    private function __construct($fromGlobal = true)
    {
        if ($fromGlobal) {
            $this->fromGlobal();
        }
    }

    public static function getInstanceMakeGlobalEmpty()
    {
        if (self::$instance == null) {
            self::$instance = new static(false);
        }

        return self::$instance;
    }

    /**
     * @param string $method HTTP method
     * @param string|UriInterface $uri URI
     * @param array $headers Request headers
     * @param string|resource|StreamInterface|null $body Request body
     * @param string $version Protocol version
     * @param array $serverParams Typically the $_SERVER superglobal
     */
    public function make(
        string $method,
        $uri,
        array $headers = [],
        $body = null,
        string $version = '1.1',
        array $serverParams = []
    ) {
        $this->serverParams = $serverParams;

        if (!($uri instanceof UriInterface)) {
            $uri = new Uri($uri);
        }

        $this->method = $method;
        $this->uri = $uri;
        $this->setHeaders($headers);
        $this->protocol = $version;

        if (!$this->hasHeader('Host')) {
            $this->updateHostFromUri();
        }

        // If we got no body, defer initialization of the stream until ServerRequest::getBody()
        if ('' !== $body && null !== $body) {
            $this->stream = Stream::create($body);
        }

        return $this;
    }

    public function fromGlobal(): ServerRequest
    {
        $this->serverParams = $_SERVER;

        /*
         * Assign method
         * */
        $this->method = $this->getMethodFromEnv();

        /*
         * Assign value ke uri
         * masih ada bug di regexnya
         * */
        $this->uri = $this->createUriFromGlobal();

        /*
         * Asign value to header
         * tidak semua webserver menyediakan function getallheader
         * */
        $headers = \function_exists('getallheaders')  ? getallheaders() : $this->getHeadersFromServer();
        $this->setHeaders($headers);

        $this->protocol = $_SERVER['SERVER_PROTOCOL'] ? $_SERVER['SERVER_PROTOCOL'] : '1.1';

        if (!$this->hasHeader('Host')) {
            $this->updateHostFromUri();
        }


        /*
         * Untuk assign value ke body
         * Variabel stream_get_contents(STDIN); tidak bisa digunakan
         * sebagai gantinya, menggunaka php input.
         * */
        $body = \fopen('php://input', 'r') ?: null;
        // If we got no body, defer initialization of the stream until ServerRequest::getBody()
        if ('' !== $body && null !== $body) {
            $this->stream = Stream::create($body);
        }

//        $cookies = empty($_COOKIE) ? array($_COOKIE) : [];
//        $this->withCookieParams($cookies);
        $this->cookieParams = $_COOKIE;

//        $gets = empty($_GET) ? array($_GET) : [];
//        $this->withQueryParams($gets);
        $this->queryParams = $_GET;

//        $posts = empty($_POST) ? array($_POST) : [];
//        $this->withParsedBody($posts);
        $this->parsedBody = $_POST;

        $this->uploadedFiles = $this->normalizeFiles();

        return $this;
    }

    public function getServerParams(): array
    {
        return $this->serverParams;
    }

    public function getUploadedFiles(): array
    {
        return $this->uploadedFiles;
    }

    public function withUploadedFiles(array $uploadedFiles)
    {
        $new = clone $this;
        $new->uploadedFiles = $uploadedFiles;

        return $new;
    }

    public function getCookieParams(): array
    {
        return $this->cookieParams;
    }

    public function withCookieParams(array $cookies)
    {
        $new = clone $this;
        $new->cookieParams = $cookies;

        return $new;
    }

    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    public function withQueryParams(array $query)
    {
        $new = clone $this;
        $new->queryParams = $query;

        return $new;
    }

    public function getParsedBody()
    {
        return $this->parsedBody;
    }

//    public function withParsedBody($data)
//    {
//        if (!\is_array($data) && !\is_object($data) && null !== $data) {
//            throw new \InvalidArgumentException('First parameter to withParsedBody MUST be object, array or null');
//        }
//
//        $new = clone $this;
//        $new->parsedBody = $data;
//
//        return $new;
//    }

    public function withParsedBody($data)
    {
        if (!\is_array($data) && !\is_object($data) && null !== $data) {
            throw new \InvalidArgumentException('First parameter to withParsedBody MUST be object, array or null');
        }

        $new = clone $this;
        $new->parsedBody = $data;

        return $new;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }
//
//    public function getAttribute($attribute, $default = null)
//    {
//        if (false === \array_key_exists($attribute, $this->attributes)) {
//            return $default;
//        }
//
//        return $this->attributes[$attribute];
//    }
//
//    public function withAttribute($attribute, $value): self
//    {
//        $new = clone $this;
//        $new->attributes[$attribute] = $value;
//
//        return $new;
//    }
//
//    public function withoutAttribute($attribute): self
//    {
//        if (false === \array_key_exists($attribute, $this->attributes)) {
//            return $this;
//        }
//
//        $new = clone $this;
//        unset($new->attributes[$attribute]);
//
//        return $new;
//    }


    public function getAttribute($attribute, $default = null)
    {
        if (false === \array_key_exists($attribute, $this->attributes)) {
            return $default;
        }

        return $this->attributes[$attribute];
    }

    public function withAttribute($attribute, $value): self
    {
        $new = clone $this;
        $new->attributes[$attribute] = $value;

        return $new;
    }

    public function withoutAttribute($attribute): self
    {
        if (false === \array_key_exists($attribute, $this->attributes)) {
            return $this;
        }

        $new = clone $this;
        unset($new->attributes[$attribute]);

        return $new;
    }


    /* BUKAN BAGIAN DARI PSR 7, TAPI BAGIAN DARI SERVER CREATOR */
    private function createUriFromGlobal(): UriInterface
    {
        $uri = new Uri('');
        $server = $_SERVER;

        if (empty($uri->getScheme())) {
            $uri = $uri->withScheme('http');
        }

        if (isset($server['HTTP_X_FORWARDED_PROTO'])) {
            $uri = $uri->withScheme($server['HTTP_X_FORWARDED_PROTO']);
        } else {
            if (isset($server['REQUEST_SCHEME'])) {
                $uri = $uri->withScheme($server['REQUEST_SCHEME']);
            } elseif (isset($server['HTTPS'])) {
                $uri = $uri->withScheme('on' === $server['HTTPS'] ? 'https' : 'http');
            }
            if (isset($server['SERVER_PORT'])) {
                $uri = $uri->withPort($server['SERVER_PORT']);
            }
        }

        if (isset($server['HTTP_HOST'])) {
            if (1 === \preg_match('/^(.+)\:(\d+)$/', $server['HTTP_HOST'], $matches)) {
                $host = (string) $matches[1];
                $port = (string) $matches[2];
                $uri = $uri->withHost($host)->withPort($port);
            } else {
                $uri = $uri->withHost($server['HTTP_HOST']);
            }
        } elseif (isset($server['SERVER_NAME'])) {
            $uri = $uri->withHost($server['SERVER_NAME']);
        }
        if (isset($server['REQUEST_URI'])) {
            $uri = $uri->withPath(\current(\explode('?', $server['REQUEST_URI'])));
        }
        if (isset($server['QUERY_STRING'])) {
            $uri = $uri->withQuery($server['QUERY_STRING']);
        }
        return $uri;
    }

    /**
     * Implementation from Zend\Diactoros\marshalHeadersFromSapi().
     */
    private function getHeadersFromServer(): array
    {
        $server = array($_SERVER);
        $headers = [];
        foreach ($server as $key => $value) {
            // Apache prefixes environment variables with REDIRECT_
            // if they are added by rewrite rules
            if (0 === \strpos($key, 'REDIRECT_')) {
                $key = \substr($key, 9);
                // We will not overwrite existing variables with the
                // prefixed versions, though
                if (\array_key_exists($key, $server)) {
                    continue;
                }
            }
            if ($value && 0 === \strpos($key, 'HTTP_')) {
                $name = \strtr(\strtolower(\substr($key, 5)), '_', '-');
                $headers[$name] = $value;
                continue;
            }
            if ($value && 0 === \strpos($key, 'CONTENT_')) {
                $name = 'content-'.\strtolower(\substr($key, 8));
                $headers[$name] = $value;
                continue;
            }
        }
        return $headers;
    }

    private function getMethodFromEnv(): string
    {
        $environment = $_SERVER;
        if (false === isset($environment['REQUEST_METHOD'])) {
            throw new \InvalidArgumentException('Cannot determine HTTP method');
        }
        return $environment['REQUEST_METHOD'];
    }


    /**
     * Return an UploadedFile instance array.
     *
     * @param array $files A array which respect $_FILES structure
     *
     * @return UploadedFileInterface[]
     *
     * @throws \InvalidArgumentException for unrecognized values
     */
    private function normalizeFiles(): array
    {
        $files = $_FILES;
        $normalized = [];
        foreach ($files as $key => $value) {
            if ($value instanceof UploadedFileInterface) {
                $normalized[$key] = $value;
            } elseif (\is_array($value) && isset($value['tmp_name'])) {
                $normalized[$key] = $this->createUploadedFileFromSpec($value);
            } elseif (\is_array($value)) {
                $normalized[$key] = $this->normalizeFiles($value);
            } else {
                throw new \InvalidArgumentException('Invalid value in files specification');
            }
        }
        return $normalized;
    }
    /**
     * Create and return an UploadedFile instance from a $_FILES specification.
     *
     * If the specification represents an array of values, this method will
     * delegate to normalizeNestedFileSpec() and return that return value.
     *
     * @param array $value $_FILES struct
     *
     * @return array|UploadedFileInterface
     */
    private function createUploadedFileFromSpec(array $value)
    {
        if (\is_array($value['tmp_name'])) {
            return $this->normalizeNestedFileSpec($value);
        }
        try {
            $stream = $this->createStreamFromFile($value['tmp_name']);
        } catch (\RuntimeException $e) {
            $stream = Stream::create('');
        }

        return $this->createUploadedFile(
            $stream,
            (int) $value['size'],
            (int) $value['error'],
            $value['name'],
            $value['type']
        );
    }

    /**
     * Normalize an array of file specifications.
     *
     * Loops through all nested files and returns a normalized array of
     * UploadedFileInterface instances.
     *
     * @param array $files
     *
     * @return UploadedFileInterface[]
     */
    private function normalizeNestedFileSpec(array $files = []): array
    {
        $normalizedFiles = [];
        foreach (\array_keys($files['tmp_name']) as $key) {
            $spec = [
                'tmp_name' => $files['tmp_name'][$key],
                'size' => $files['size'][$key],
                'error' => $files['error'][$key],
                'name' => $files['name'][$key],
                'type' => $files['type'][$key],
            ];
            $normalizedFiles[$key] = $this->createUploadedFileFromSpec($spec);
        }
        return $normalizedFiles;
    }

    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        $resource = @\fopen($filename, $mode);
        if (false === $resource) {
            if ('' === $mode || false === \in_array($mode[0], ['r', 'w', 'a', 'x', 'c'])) {
                throw new \InvalidArgumentException('The mode ' . $mode . ' is invalid.');
            }
            throw new \RuntimeException('The file ' . $filename . ' cannot be opened.');
        }
        return Stream::create($resource);
    }

    public function createUploadedFile(
        StreamInterface $stream,
        int $size = null,
        int $error = \UPLOAD_ERR_OK,
        string $clientFilename = null,
        string $clientMediaType = null
    ): UploadedFileInterface {
        if (null === $size) {
            $size = $stream->getSize();
        }
        return new UploadedFile($stream, $size, $error, $clientFilename, $clientMediaType);
    }


    // need DOTKey
    public function query(string $key, $default = '')
    {
        $dotKey = new Dot($this->getQueryParams());

        return $dotKey->get($key, $default);
    }

    public function hasQuery(string $key)
    {
        $dotKey = new Dot($this->queryParams);

        return $dotKey->has($key);
    }

    public function input(string $key, $default = '')
    {
        $dotkey = new Dot($this->parsedBody);

        return $dotkey->get($key, $default);
    }

    public function hasInput(string $key)
    {
        $dotkey = new Dot($this->parsedBody);

        return $dotkey->has($key);
    }
}
