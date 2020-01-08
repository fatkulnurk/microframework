<?php
declare(strict_types=1);

namespace Fatkulnurk\Microframework\Http\Data;
use Spatie\ArrayToXml\ArrayToXml;

class Xml implements ResponseDataInterface
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }
    
    public function proses()
    {
        return ArrayToXml::create($this->data);
    }
}