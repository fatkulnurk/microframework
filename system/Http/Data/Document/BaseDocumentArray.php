<?php
namespace Fatkulnurk\Microframework\Http\Data\Document;

abstract class BaseDocumentArray implements DocumentArray
{
    protected $data = [];
    protected $result = '';

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }
}