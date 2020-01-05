<?php
namespace Fatkulnurk\Microframework\Http\Data\Document;

interface DocumentFactory
{
    public function createFromArray(array $data);
    public function createFromObject(object $data);
}