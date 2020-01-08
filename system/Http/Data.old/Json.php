<?php

declare(strict_types=1);

namespace Fatkulnurk\Microframework\Http\Data;

class Json implements ResponseDataInterface
{
    protected $data;

    public function __construct(array $data = array(['message' => 'data null']))
    {
        $this->data = $data;
    }

    public function proses()
    {
        return json_encode($this->data);
    }
}