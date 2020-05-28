<?php
namespace Fatkulnurk\Microframework\Http\Data\Document;

class XmlDocumentArray extends BaseDocumentArray implements DocumentArray
{
    public function proses(): void
    {
        if (is_array($this->data)) {
            $this->result = \Spatie\ArrayToXml\ArrayToXml::convert($this->data);
        }

        $this->result = \Spatie\ArrayToXml\ArrayToXml::convert($this->data);
    }

    public function result()
    {
        $this->proses();

        return $this->result;
    }
}
