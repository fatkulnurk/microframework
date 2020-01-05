<?php
namespace Fatkulnurk\Microframework\Http\Data\View;

class LexTemplateFactory implements TemplateFactory
{
    public function createPageTemplate(string $path = ''): PageTemplate
    {
        return new LexPageTemplate($path);
    }

    public function getRenderer(): TemplateRenderer
    {
        return new LexRenderer();
    }
}