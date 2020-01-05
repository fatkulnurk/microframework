<?php
namespace Fatkulnurk\Microframework\Http\Data\View;

class LexPageTemplate extends BasePageTemplate implements PageTemplate
{
    public function openFileTemplate()
    {
        $this->templateString = file_get_contents($this->getLocation(). '.lex');
    }

    public function getTemplateString()
    {
        return $this->templateString;
    }
}