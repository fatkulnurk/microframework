<?php
use Fatkulnurk\Microframework\App;
use PHPUnit\Framework\TestCase;

class AppTest extends TestCase
{
    public function testUniqueInstanceApp()
    {
        $object1 = App::getInstance();
        $object2 = App::getInstance();

        $this->assertInstanceOf(App::class, $object1);
        $this->assertSame($object1, $object2);
    }

    public function testAppPath()
    {
        $app = App::getInstance()->setPath(__DIR__);

        $this->assertSame(__DIR__, $app->getPath());
    }

    public function testAppConfig()
    {
        $app = App::getInstance()->setConfig([
            'name' => 'framework'
        ]);

        $this->assertSame('framework', $app->getConfig('name'));
    }
}
