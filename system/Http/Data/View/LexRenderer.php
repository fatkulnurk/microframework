<?php
namespace Fatkulnurk\Microframework\Http\Data\View;

class LexRenderer implements TemplateRenderer
{
    public function render(string $templateString, array $arguments = []): string
    {
        $parser = new \Lex\Parser();
        $template = $parser->parse($templateString, $arguments);

        return $template;
    }
}