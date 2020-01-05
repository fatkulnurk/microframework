<?php
namespace Fatkulnurk\Microframework\Http\Data\View;

/**
 * The Abstract Factory interface declares creation methods for each distinct
 * product type.
 */
interface TemplateFactory
{
    public function createPageTemplate(string $path): PageTemplate;

    public function getRenderer(): TemplateRenderer;

}
