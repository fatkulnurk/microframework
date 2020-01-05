<?php
namespace Fatkulnurk\Microframework\Http\Data\Document;

class JsonFactory implements DocumentFactory
{
    public function createFromObject(object $data)
    {
        return new JsonDocumentObject($data);
    }

    public function createFromArray(array $data)
    {
        return new JsonDocumentArray($data);
    }
}