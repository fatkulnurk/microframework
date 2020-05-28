<?php
namespace Fatkulnurk\Microframework\Http\Data\View;

class TwigPageTemplate extends BasePageTemplate implements PageTemplate
{
    public function openFileTemplate()
    {
        //$this->templateString = file_get_contents($this->getBaseLocation() . '/' .$this->getLocation(). '.twig.php');
        $this->templateString = $this->getLocation();
    }

    public function getTemplateString()
    {
        $this->openFileTemplate();
        return $this->templateString;
    }
}
