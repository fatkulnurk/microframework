<?php
namespace Fatkulnurk\Microframework\Http\Data\Document;

class XmlDocumentObject extends BaseDocumentObject implements DocumentObject
{
    public function convert()
    {
        return (object) $this->data;
    }

    public function proses()
    {
        $converter = new \SalernoLabs\PHPToXML\Convert();
        try {
            $xml = $converter
                ->setObjectData($this->data)
                ->convert();
        } catch (Exception $e) {
            throw new Exception($e);
        }
    }

    public function result()
    {
        $this->proses();
        $this->convert();

        return $this->result;
    }
}