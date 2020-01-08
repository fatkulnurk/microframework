<?php

namespace Fatkulnurk\Microframework\Http\Data;

class ViewFactory
{
    private $active;
    private $data;
    private $path;

    public function __construct($path, $data, $active = null)
    {
        $this->path = $path;
        $this->data = $data;
        $this->active = $active;
    }

    public function render()
    {
        switch ($this->active) {
            case 'lex':
                return (new ViewLex($this->path, $this->data));
            default:
                return (new ViewTwig($this->path, $this->data));
        }
    }
}