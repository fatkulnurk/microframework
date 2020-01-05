<?php

declare(strict_types=1);

namespace Fatkulnurk\Microframework\Http\Data;

class Redirect implements ResponseDataInterface
{
    private $data;
    private $permanent;
    private $code;

    public function __construct(string $data, $permanent = false, $code = 301)
    {
        $this->data = $data;
        $this->permanent = $permanent;
        $this->code = $code;
    }

    public function proses()
    {
        header('Location ' . $this->data, $this->permanent, $this->code);
    }
}