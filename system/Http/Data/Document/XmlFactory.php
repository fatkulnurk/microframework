<?php
namespace Fatkulnurk\Microframework\Http\Data\Document;

class XmlFactory implements DocumentFactory
{
    public function createFromObject(object $data)
    {
        return new XmlDocumentObject($data);
    }

    public function createFromArray(array $data)
    {
        return new XmlDocumentArray($data);
    }
}