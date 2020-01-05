<?php

declare(strict_types=1);

namespace Fatkulnurk\Microframework\Http\Data;

use phpDocumentor\Reflection\Types\This;

class ViewTwig implements ViewInterface
{
    private $twig;

    private $path;
    private $data;

    public function __construct($path, $data)
    {
        $this->data = $data;
        $this->path = $path;

        $this->setEnvironment();
    }

    public function render()
    {
        return $this->twig>render($this->path, $this->data);
    }

    private function setEnvironment()
    {
        $loader = new \Twig\Loader\FilesystemLoader('path_template');
        $twig = new \Twig\Environment($loader);
        return $twig;
    }
}