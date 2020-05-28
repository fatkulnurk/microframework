<?php
use Fatkulnurk\Microframework\App;
use Fatkulnurk\Microframework\Core\Config;
use Fatkulnurk\Microframework\Http\Message\Request;
use Fatkulnurk\Microframework\Http\Message\Response;
use Fatkulnurk\Microframework\Http\Message\ServerRequest;
use PHPUnit\Framework\TestCase;

class SingletonTest extends TestCase
{
    public function testUniqueInstanceApp()
    {
        $object1 = App::getInstance();
        $object2 = App::getInstance();

        $this->assertInstanceOf(App::class, $object1);
        $this->assertSame($object1, $object2);
    }

    public function testUniqueInstanceResponse()
    {
        $object1 = Response::getInstance();
        $object2 = Response::getInstance();

        $this->assertInstanceOf(Response::class, $object1);
        $this->assertSame($object1, $object2);
    }

    public function testUniqueInstanceRequest()
    {
        $object1 = Request::getInstance();
        $object2 = Request::getInstance();

        $this->assertInstanceOf(Request::class, $object1);
        $this->assertSame($object1, $object2);
    }

    public function testUniqueInstanceServerRequest()
    {
        $object1 = ServerRequest::getInstanceMakeGlobalEmpty();
        $object2 = ServerRequest::getInstanceMakeGlobalEmpty();

        $this->assertInstanceOf(ServerRequest::class, $object1);
        $this->assertSame($object1, $object2);
    }

    public function testUniqueInstanceConfig()
    {
        $object1 = Config::getInstance();
        $object2 = Config::getInstance();

        $this->assertInstanceOf(Config::class, $object1);
        $this->assertSame($object1, $object2);
    }
}
