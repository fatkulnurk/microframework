<?php
namespace Fatkulnurk\Microframework\Http\Data\View;

class TwigTemplateFactory implements TemplateFactory
{
    public function createPageTemplate(string $path = ''): PageTemplate
    {
        return new TwigPageTemplate($path);
    }

    public function getRenderer(): TemplateRenderer
    {
        return new TwigRenderer();
    }
}