<?php
use Fatkulnurk\Microframework\Http\Message\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

class ResponseTest extends TestCase
{
    public function testDefaultGetInstance()
    {
        $r = Response::getInstance();
        $this->assertSame(200, $r->getStatusCode());
        $this->assertSame('1.1', $r->getProtocolVersion());
        $this->assertSame('OK', $r->getReasonPhrase());
        $this->assertSame([], $r->getHeaders());
        $this->assertInstanceOf(StreamInterface::class, $r->getBody());
        $this->assertSame('', (string) $r->getBody());
    }

    public function testCanGetInstanceWithStatusCode()
    {
        $r = Response::getInstance()->withStatus(404);
        $this->assertSame(404, $r->getStatusCode());
        $this->assertSame('Not Found', $r->getReasonPhrase());
    }

//    public function testCanGetInstanceWithUndefinedStatusCode()
//    {
//        $r = Response::getInstance()->withStatus(999);
//        $this->assertSame(999, $r->getStatusCode());
//        $this->assertSame('', $r->getReasonPhrase());
//    }

    public function testCanGetInstanceWithStatusCodeAndEmptyReason()
    {
        $r = Response::getInstance()->make(404, [], '', '1.1', '');
        $this->assertSame(404, $r->getStatusCode());
        $this->assertSame('', $r->getReasonPhrase());
    }

    public function testGetInstanceDoesNotReadStreamBody()
    {
        try {
            $body = $this->getMockBuilder(StreamInterface::class)->getMock();
        } catch (ReflectionException $e) {
        }
        $body->expects($this->never())
            ->method('__toString');

        $r = Response::getInstance()->make(200, [], $body);
        $this->assertSame($body, $r->getBody());
    }

    public function testStatusCanBeNumericString()
    {
        $r = Response::getInstance()->withStatus(404);
        $r2 = $r->withStatus('201');
        $this->assertSame(404, $r->getStatusCode());
        $this->assertSame('Not Found', $r->getReasonPhrase());
        $this->assertSame(201, $r2->getStatusCode());
        $this->assertSame('Created', $r2->getReasonPhrase());
    }

//    public function testCanGetInstanceWithHeaders()
//    {
//        $r = new Response(200, ['Foo' => 'Bar']);
//        $this->assertSame(['Foo' => ['Bar']], $r->getHeaders());
//        $this->assertSame('Bar', $r->getHeaderLine('Foo'));
//        $this->assertSame(['Bar'], $r->getHeader('Foo'));
//    }

//    public function testCanGetInstanceWithHeadersAsArray()
//    {
////        $r = new Response(200, [
////            'Foo' => ['baz', 'bar'],
////        ]);
//        $r = Response::getInstance()->withStatus(200)
//            ->withHeader('Foo', ['baz', 'bar']);
//        $this->assertSame(['Foo' => ['baz', 'bar']], $r->getHeaders());
//        $this->assertSame('baz, bar', $r->getHeaderLine('Foo'));
//        $this->assertSame(['baz', 'bar'], $r->getHeader('Foo'));
//    }

//    public function testCanConstructWithBody()
//    {
////        $r = new Response(200, [], 'baz');
//        $r = Response::getInstance()->withBody('baz');
//        $this->assertInstanceOf(StreamInterface::class, $r->getBody());
//        $this->assertSame('baz', (string) $r->getBody());
//    }

    public function testEmptyBody()
    {
        $stream = \Fatkulnurk\Microframework\Http\Message\Stream::create('');
        $r = Response::getInstance()
            ->withStatus(200)
            ->withBody($stream);
        $this->assertInstanceOf(StreamInterface::class, $r->getBody());
        $this->assertSame('', (string) $r->getBody());
    }

    public function testFalseyBody()
    {
        $stream = \Fatkulnurk\Microframework\Http\Message\Stream::create('0');
        $r = Response::getInstance()->withStatus(200)->withBody($stream);
        $this->assertInstanceOf(StreamInterface::class, $r->getBody());
        $this->assertSame('0', (string) $r->getBody());
    }

//    public function testCanConstructWithReason()
//    {
//        $r = Response::getInstance()->make(200, [], null, '1.1', 'bar');
//        $this->assertSame('bar', $r->getReasonPhrase());
//
//        $r = new Response(200, [], null, '1.1', '0');
//        $this->assertSame('0', $r->getReasonPhrase(), 'Falsey reason works');
//    }
//
//    public function testCanConstructWithProtocolVersion()
//    {
//        $r = Response::getInstance()->make(200, [], null, '1000');
//        $this->assertSame('1000', $r->getProtocolVersion());
//    }

    public function testWithStatusCodeAndNoReason()
    {
        $r = Response::getInstance()->withStatus(201);
        $this->assertSame(201, $r->getStatusCode());
        $this->assertSame('Created', $r->getReasonPhrase());
    }

    public function testWithStatusCodeAndReason()
    {
        $r = Response::getInstance()->withStatus(201, 'Foo');
        $this->assertSame(201, $r->getStatusCode());
        $this->assertSame('Foo', $r->getReasonPhrase());

        $r = Response::getInstance()->withStatus(201, '0');
        $this->assertSame(201, $r->getStatusCode());
        $this->assertSame('0', $r->getReasonPhrase(), 'Falsey reason works');
    }

    public function testWithProtocolVersion()
    {
        $r = Response::getInstance()->withProtocolVersion('1000');
        $this->assertSame('1000', $r->getProtocolVersion());
    }

    public function testSameInstanceWhenSameProtocol()
    {
        $r = Response::getInstance();
        $this->assertSame($r, $r->withProtocolVersion('1.1'));
    }

    public function testWithBody()
    {
        $b = (new \Nyholm\Psr7\Factory\Psr17Factory())->createStream('0');
        $r = Response::getInstance()->withBody($b);
        $this->assertInstanceOf(StreamInterface::class, $r->getBody());
        $this->assertSame('0', (string) $r->getBody());
    }

    public function testSameInstanceWhenSameBody()
    {
        $r = Response::getInstance();
        $b = $r->getBody();
        $this->assertSame($r, $r->withBody($b));
    }

    public function testWithHeader()
    {
        $r = Response::getInstance()
            ->withStatus(200)
            ->withHeader('Foo', 'Bar');
        $r2 = $r->withHeader('baZ', 'Bam');
        $this->assertSame(['Foo' => ['Bar']], $r->getHeaders());
        $this->assertSame(['Foo' => ['Bar'], 'baZ' => ['Bam']], $r2->getHeaders());
        $this->assertSame('Bam', $r2->getHeaderLine('baz'));
        $this->assertSame(['Bam'], $r2->getHeader('baz'));
    }

    public function testWithHeaderAsArray()
    {
        $r = Response::getInstance()->withStatus(200)
            ->withHeader('Foo', 'Bar');
        $r2 = $r->withHeader('baZ', ['Bam', 'Bar']);
        $this->assertSame(['Foo' => ['Bar']], $r->getHeaders());
        $this->assertSame(['Foo' => ['Bar'], 'baZ' => ['Bam', 'Bar']], $r2->getHeaders());
        $this->assertSame('Bam, Bar', $r2->getHeaderLine('baz'));
        $this->assertSame(['Bam', 'Bar'], $r2->getHeader('baz'));
    }

    public function testWithHeaderReplacesDifferentCase()
    {
        $r = Response::getInstance()->withStatus(200)
            ->withHeader('Foo', 'Bar');
        $r2 = $r->withHeader('foO', 'Bam');
        $this->assertSame(['Foo' => ['Bar']], $r->getHeaders());
        $this->assertSame(['foO' => ['Bam']], $r2->getHeaders());
        $this->assertSame('Bam', $r2->getHeaderLine('foo'));
        $this->assertSame(['Bam'], $r2->getHeader('foo'));
    }

    public function testWithAddedHeader()
    {
        $r = Response::getInstance()->withStatus(200)
            ->withHeader('Foo', 'Bar');
        $r2 = $r->withAddedHeader('foO', 'Baz');
        $this->assertSame(['Foo' => ['Bar']], $r->getHeaders());
        $this->assertSame(['Foo' => ['Bar', 'Baz']], $r2->getHeaders());
        $this->assertSame('Bar, Baz', $r2->getHeaderLine('foo'));
        $this->assertSame(['Bar', 'Baz'], $r2->getHeader('foo'));
    }

    public function testWithAddedHeaderAsArray()
    {
        $r = Response::getInstance()->withStatus(200)
            ->withHeader('Foo', 'Bar');
        $r2 = $r->withAddedHeader('foO', ['Baz', 'Bam']);
        $this->assertSame(['Foo' => ['Bar']], $r->getHeaders());
        $this->assertSame(['Foo' => ['Bar', 'Baz', 'Bam']], $r2->getHeaders());
        $this->assertSame('Bar, Baz, Bam', $r2->getHeaderLine('foo'));
        $this->assertSame(['Bar', 'Baz', 'Bam'], $r2->getHeader('foo'));
    }

    public function testWithAddedHeaderThatDoesNotExist()
    {
        $r = Response::getInstance()->withStatus(200)
            ->withHeader('Foo', 'Bar');
        $r2 = $r->withAddedHeader('nEw', 'Baz');
        $this->assertSame(['Foo' => ['Bar']], $r->getHeaders());
        $this->assertSame(['Foo' => ['Bar'], 'nEw' => ['Baz']], $r2->getHeaders());
        $this->assertSame('Baz', $r2->getHeaderLine('new'));
        $this->assertSame(['Baz'], $r2->getHeader('new'));
    }

    public function testWithoutHeaderThatExists()
    {
        $r = Response::getInstance()->withStatus(200)
            ->withHeader('Foo', 'Bar')
            ->withHeader('Baz', 'Bam');
        $r2 = $r->withoutHeader('foO');
        $this->assertTrue($r->hasHeader('foo'));
        $this->assertSame(['Foo' => ['Bar'], 'Baz' => ['Bam']], $r->getHeaders());
        $this->assertFalse($r2->hasHeader('foo'));
        $this->assertSame(['Baz' => ['Bam']], $r2->getHeaders());
    }

    public function testWithoutHeaderThatDoesNotExist()
    {
        $r = Response::getInstance()->withStatus(200)->withHeader('Baz', 'Bam');
        $r2 = $r->withoutHeader('foO');
        $this->assertSame($r, $r2);
        $this->assertFalse($r2->hasHeader('foo'));
        $this->assertSame(['Baz' => ['Bam']], $r2->getHeaders());
    }

    public function testSameInstanceWhenRemovingMissingHeader()
    {
        $r = Response::getInstance();
        $this->assertSame($r, $r->withoutHeader('foo'));
    }

    public function trimmedHeaderValues()
    {
        return [
            [Response::getInstance()->withHeader('OWS', " \t \tFoo\t \t ")],
            [Response::getInstance()->withHeader('OWS', " \t \tFoo\t \t ")],
            [Response::getInstance()->withAddedHeader('OWS', " \t \tFoo\t \t ")],
        ];
    }

    /**
     * @dataProvider trimmedHeaderValues
     */
    public function testHeaderValuesAreTrimmed($r)
    {
        $this->assertSame(['OWS' => ['Foo']], $r->getHeaders());
        $this->assertSame('Foo', $r->getHeaderLine('OWS'));
        $this->assertSame(['Foo'], $r->getHeader('OWS'));
    }
}
