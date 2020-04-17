<?php
namespace Fatkulnurk\Microframework\Http\Data\View;

use Fatkulnurk\Microframework\App;

class LexPageTemplate extends BasePageTemplate implements PageTemplate
{
    public function openFileTemplate()
    {
//        $this->templateString = file_get_contents($this->getLocation(). '.lex');
        $this->templateString = file_get_contents(
            App::getInstance()->getPath() . "./../src/views/" . $this->getLocation()
        );
    }

    public function getTemplateString()
    {
        return $this->templateString;
    }
}
