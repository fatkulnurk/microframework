<?php

declare(strict_types=1);

namespace Fatkulnurk\Microframework\Http\Data;

class ViewLex implements ViewInterface
{
    private $path;
    private $data;

    public function __construct($path, $data)
    {
        $this->data = $data;
        $this->path = $path;
    }

    public function render()
    {
        $parser = new Lex\Parser();
        $template = $parser->parse(file_get_contents('template.lex'), $this->data);

        return $template;
    }
}