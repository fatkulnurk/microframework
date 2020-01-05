<?php

declare(strict_types=1);

namespace Fatkulnurk\Microframework\Http\Data;

use phpDocumentor\Reflection\Types\This;

class Download implements ResponseDataInterface
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function proses()
    {
        return readfile($this->data);
    }

    public function setHeaders(): void
    {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($this->data));
        flush(); // Flush system output buffer
    }

    public function setContentDisposition(): void
    {
        header('Content-Disposition: attachment; filename="'.basename($this->data).'"');
    }
}