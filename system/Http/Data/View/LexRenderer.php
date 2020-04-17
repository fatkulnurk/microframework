<?php
namespace Fatkulnurk\Microframework\Http\Data\View;

use Fatkulnurk\Microframework\System\Core\Exception\TemplateEngineNotFoundException;

class LexRenderer implements TemplateRenderer
{
    public function render(string $templateString, array $arguments = []): string
    {
        if (class_exists(\Lex\Parser::class)) {
            $parser = new \Lex\Parser();
            $template = $parser->parse($templateString, $arguments);

            return $template;
        }

        throw new TemplateEngineNotFoundException('Lex not installed');
    }
}