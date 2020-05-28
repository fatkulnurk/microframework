<?php

use Fatkulnurk\Microframework\Http\Message\Request;
use Fatkulnurk\Microframework\Http\Message\Uri;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Fatkulnurk\Microframework\Http\Message\Stream;

class RequestTest extends TestCase
{
    public function testRequestUriMayBeString()
    {
        $r = Request::getInstance()->make('GET', '/');
        $this->assertEquals('/', (string) $r->getUri());
    }

    public function testRequestUriMayBeUri()
    {
        $uri = new Uri('/');
        $r = Request::getInstance()->make('GET', $uri);
        $this->assertSame($uri, $r->getUri());
    }

    public function testValidateRequestUri()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unable to parse URI: ///');

        Request::getInstance()->make('GET', '///');
    }

    public function testCanConstructWithBody()
    {
        $r = Request::getInstance()->make('GET', '/', [], 'baz');
        $this->assertInstanceOf(StreamInterface::class, $r->getBody());
        $this->assertEquals('baz', (string) $r->getBody());
    }

    public function testNullBody()
    {
        $r = Request::getInstance()->make('GET', '/', [])->withBody(Stream::create(''));
        $this->assertInstanceOf(StreamInterface::class, $r->getBody());
        $this->assertSame('', (string) $r->getBody());
    }

    public function testFalseyBody()
    {
        $r = Request::getInstance()->make('GET', '/', [], '0');
        $this->assertInstanceOf(StreamInterface::class, $r->getBody());
        $this->assertSame('0', (string) $r->getBody());
    }

    public function testConstructorDoesNotReadStreamBody()
    {
        $body = $this->getMockBuilder(StreamInterface::class)->getMock();
        $body->expects($this->never())
            ->method('__toString');

        $r = Request::getInstance()->make('GET', '/', [], $body);
        $this->assertSame($body, $r->getBody());
    }

    public function testWithUri()
    {
        $r1 = Request::getInstance()->make('GET', '/');
        $u1 = $r1->getUri();
        $u2 = new Uri('http://www.example.com');
        $r2 = $r1->withUri($u2);
        $this->assertNotSame($r1, $r2);
        $this->assertSame($u2, $r2->getUri());
        $this->assertSame($u1, $r1->getUri());

        $r3 = Request::getInstance()->make('GET', '/');
        $u3 = $r3->getUri();
        $r4 = $r3->withUri($u3);
        $this->assertSame($r3, $r4, 'If the Request did not change, then there is no need to create a new request object');

        $u4 = new Uri('/');
        $r5 = $r3->withUri($u4);
        $this->assertNotSame($r3, $r5);
    }

    public function testSameInstanceWhenSameUri()
    {
        $r1 = Request::getInstance()->make('GET', 'http://foo.com');
        $r2 = $r1->withUri($r1->getUri());
        $this->assertSame($r1, $r2);
    }

    public function testWithRequestTarget()
    {
        $r1 = Request::getInstance()->make('GET', '/');
        $r2 = $r1->withRequestTarget('*');
        $this->assertEquals('*', $r2->getRequestTarget());
        $this->assertEquals('/', $r1->getRequestTarget());
    }

    public function testWithInvalidRequestTarget()
    {
        $r = Request::getInstance()->make('GET', '/');
        $this->expectException(\InvalidArgumentException::class);
        $r->withRequestTarget('foo bar');
    }

    public function testGetRequestTarget()
    {
        $r = Request::getInstance()->make('GET', 'https://dibumi.com');
        $this->assertEquals('/', $r->getRequestTarget());

        $r = Request::getInstance()->make('GET', 'https://dibumi.com/foo?bar=baz');
        $this->assertEquals('/foo?bar=baz', $r->getRequestTarget());

        $r = Request::getInstance()->make('GET', 'https://dibumi.com?bar=baz');
        $this->assertEquals('/?bar=baz', $r->getRequestTarget());
    }

    public function testRequestTargetDoesNotAllowSpaces()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid request target provided; cannot contain whitespace');

        $r1 = Request::getInstance()->make('GET', '/');
        $r1->withRequestTarget('/foo bar');
    }

    public function testRequestTargetDefaultsToSlash()
    {
        $r1 = Request::getInstance()->make('GET', '');
        $this->assertEquals('/', $r1->getRequestTarget());
        $r2 = Request::getInstance()->make('GET', '*');
        $this->assertEquals('*', $r2->getRequestTarget());
        $r3 = Request::getInstance()->make('GET', 'http://foo.com/bar baz/');
        $this->assertEquals('/bar%20baz/', $r3->getRequestTarget());
    }

    public function testBuildsRequestTarget()
    {
        $r1 = Request::getInstance()->make('GET', 'http://foo.com/baz?bar=bam');
        $this->assertEquals('/baz?bar=bam', $r1->getRequestTarget());
    }

    public function testBuildsRequestTargetWithFalseyQuery()
    {
        $r1 = Request::getInstance()->make('GET', 'http://foo.com/baz?0');
        $this->assertEquals('/baz?0', $r1->getRequestTarget());
    }

    public function testHostIsAddedFirst()
    {
        $r = Request::getInstance()->make('GET', 'http://foo.com/baz?bar=bam', ['Foo' => 'Bar']);
        $this->assertEquals([
            'Host' => ['foo.com'],
            'Foo' => ['Bar'],
        ], $r->getHeaders());
    }

    public function testCanGetHeaderAsCsv()
    {
        $r = Request::getInstance()
            ->flushData()
            ->make('GET', 'http://foo.com/baz?bar=bam', [
            'Foo' => ['a', 'b', 'c'],
        ]);
        $this->assertEquals('a, b, c', $r->getHeaderLine('Foo'));
        $this->assertEquals('', $r->getHeaderLine('Bar'));
    }

    public function testHostIsNotOverwrittenWhenPreservingHost()
    {
        $r = Request::getInstance()
            ->flushData()
            ->make('GET', 'http://foo.com/baz?bar=bam', ['Host' => 'a.com']);
        $this->assertEquals(['Host' => ['a.com']], $r->getHeaders());
        $r2 = $r->withUri(new Uri('http://www.foo.com/bar'), true);
        $this->assertEquals('a.com', $r2->getHeaderLine('Host'));
    }

    public function testOverridesHostWithUri()
    {
        $r = Request::getInstance()
            ->flushData()
            ->make('GET', 'http://foo.com/baz?bar=bam');
        $this->assertEquals(['Host' => ['foo.com']], $r->getHeaders());
        $r2 = $r->withUri(new Uri('http://www.baz.com/bar'));
        $this->assertEquals('www.baz.com', $r2->getHeaderLine('Host'));
    }

    public function testAggregatesHeaders()
    {
        $r = Request::getInstance()
            ->flushData()
            ->make('GET', '', [
            'ZOO' => 'zoobar',
            'zoo' => ['foobar', 'zoobar'],
        ]);
        $this->assertEquals(['ZOO' => ['zoobar', 'foobar', 'zoobar']], $r->getHeaders());
        $this->assertEquals('zoobar, foobar, zoobar', $r->getHeaderLine('zoo'));
    }

    public function testSupportNumericHeaders()
    {
        $r = Request::getInstance()
            ->flushData()
            ->make('GET', '', [
            'Content-Length' => 200,
        ]);
        $this->assertSame(['Content-Length' => ['200']], $r->getHeaders());
        $this->assertSame('200', $r->getHeaderLine('Content-Length'));
    }

    public function testAddsPortToHeader()
    {
        $r = Request::getInstance()->make('GET', 'http://foo.com:8124/bar');
        $this->assertEquals('foo.com:8124', $r->getHeaderLine('host'));
    }

    public function testAddsPortToHeaderAndReplacePreviousPort()
    {
        $r = Request::getInstance()->make('GET', 'http://foo.com:8124/bar');
        $r = $r->withUri(new Uri('http://foo.com:8125/bar'));
        $this->assertEquals('foo.com:8125', $r->getHeaderLine('host'));
    }

    public function testCannotHaveHeaderWithEmptyName()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Header name must be an RFC 7230 compatible string.');
        $r = Request::getInstance()->make('GET', 'https://example.com/');
        $r->withHeader('', 'Bar');
    }

    public function testCanHaveHeaderWithEmptyValue()
    {
        $r = Request::getInstance()->make('GET', 'https://example.com/');
        $r = $r->withHeader('Foo', '');
        $this->assertEquals([''], $r->getHeader('Foo'));
    }

    public function testUpdateHostFromUri()
    {
        $request = Request::getInstance()->make('GET', '/');
        $request = $request->withUri(new Uri('https://domain.tld'));
        $this->assertEquals('domain.tld', $request->getHeaderLine('Host'));

        $request = Request::getInstance()
            ->flushData()
            ->make('GET', new Uri('https://example.com/'));
        $this->assertEquals('example.com', $request->getHeaderLine('Host'));
        $request = $request->withUri(new Uri('https://domain.tld'));
        $this->assertEquals('domain.tld', $request->getHeaderLine('Host'));

        $request = Request::getInstance()->make('GET', '/');
        $request = $request->withUri(new Uri('https://domain.tld:8080'));
        $this->assertEquals('domain.tld:8080', $request->getHeaderLine('Host'));

        $request = Request::getInstance()->make('GET', '/');
        $request = $request->withUri(new Uri('https://domain.tld:443'));
        $this->assertEquals('domain.tld', $request->getHeaderLine('Host'));
    }
}
