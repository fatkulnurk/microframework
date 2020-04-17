<?php
namespace Tests\Microframework;

use FatkulNurK\Microframework\Http\Message\Uri;
use PHPUnit\Framework\TestCase;

class UriTest extends TestCase
{
    public function testUriParse()
    {

        $uri = new Uri('https://user:pass@example.com:8080/path/123?q=abc#test');

        $this->assertSame('https', $uri->getScheme());
        $this->assertSame('user:pass@example.com:8080', $uri->getAuthority());
        $this->assertSame('user:pass', $uri->getUserInfo());
        $this->assertSame('example.com', $uri->getHost());
        $this->assertSame(8080, $uri->getPort());
        $this->assertSame('/path/123', $uri->getPath());
        $this->assertSame('q=abc', $uri->getQuery());
        $this->assertSame('test', $uri->getFragment());
        $this->assertSame('https://user:pass@example.com:8080/path/123?q=abc#test', (string) $uri);
    }
}