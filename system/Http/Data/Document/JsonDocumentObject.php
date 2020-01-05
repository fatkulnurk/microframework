<?php
namespace Fatkulnurk\Microframework\Http\Data\Document;

class JsonDocumentObject extends BaseDocumentObject implements DocumentObject
{
    public function convert()
    {
        return (object) $this->data;
    }

    public function proses()
    {
        if (is_object($this->data)) {
            return json_encode($this->data);
        }

        return json_encode($this->convert());
    }

    public function result()
    {
        $this->proses();
        $this->convert();

        return $this->result;
    }
}
