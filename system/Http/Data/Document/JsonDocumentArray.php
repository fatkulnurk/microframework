<?php
namespace Fatkulnurk\Microframework\Http\Data\Document;

class JsonDocumentArray extends BaseDocumentArray implements DocumentArray
{
    public function proses(): void
    {
        if (is_array($this->data)) {
            $this->result = json_encode($this->data);
        }

        $this->result = json_encode((array) $this->data);
    }

    public function result()
    {
        $this->proses();

        return $this->result;
    }
}