<?php
namespace Fatkulnurk\Microframework\Http\Data\Document;

class BaseDocumentObject
{
    protected $data;
    protected $dataArray = [];
    protected $result = '';

    public function __construct(object $data)
    {
        $this->data = $data;
    }
}
