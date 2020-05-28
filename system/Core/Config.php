<?php
declare(strict_types=1);

namespace Mifa\Core;

//use Noodlehaus\Config;
//use Noodlehaus\Parser\Json;

//https://github.com/hassankhan/config
use Fatkulnurk\Microframework\Core\Singleton;

class Config
{
    use Singleton;
    private $patConfig;

    public function read()
    {
        $config = new \Noodlehaus\Config($this->patConfig);
        return $config;
    }

    public function setPathConfig($pathConfig)
    {
        $this->patConfig = $pathConfig;
        return $this;
    }

    public function getPathConfig($pathConfig)
    {
        return $this->patConfig;
    }
}
