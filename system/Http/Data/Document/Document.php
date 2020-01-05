<?php
namespace Fatkulnurk\Microframework\Http\Data\Document;

class Document
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function result(DocumentFactory $documentFactory)
    {
        if (is_object($this->data)) {
            $result = $documentFactory->createFromObject($this->data);
        } else {
            $result = $documentFactory->createFromArray($this->data);
        }

        return $result->result();
    }
}

// CONTOH PENGGUNAAN
//$data = [
//    'nama' => 'fatkul nur k'
//];
//
//$dataObj = new Std();
//$dataObj->name = 'fatkul nur k';
//$dataObj->birthday = '18 januari 1999';
//
//
//$document = new Document($data);
//$result = $document->result(new JsonFactory());
//
//$document = new Document($dataObj);
//$result = $document->result(new JsonFactory());
