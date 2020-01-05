<?php
declare(strict_types=1);

namespace Mifa\Core;
//use Noodlehaus\Config;
//use Noodlehaus\Parser\Json;

//https://github.com/hassankhan/config
class Config
{
    private $patConfig;

    public function read()
    {
        $config = new Noodlehaus\Config($this->patConfig);
        return $config;
    }
}
