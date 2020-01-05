<?php
declare(strict_types=1);

namespace Fatkulnurk\Microframework\Http\Message;

use Fatkulnurk\Microframework\Core\Singleton;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class Request implements RequestInterface
{
    use MessageTrait;
    use RequestTrait;
    use Singleton;

    /**
     * @param string $method HTTP method
     * @param string|UriInterface $uri URI
     * @param array $headers Request headers
     * @param string|resource|StreamInterface|null $body Request body
     * @param string $version Protocol version
     */
    public function make(string $method, $uri, array $headers = [], $body = null, string $version = '1.1')
    {
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

        // Request::getBody()
        if ('' !== $body && null !== $body) {
            $this->stream = Stream::create($body);
        }

        return $this;
    }
}
