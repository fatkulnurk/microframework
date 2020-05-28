<?php
namespace Fatkulnurk\Microframework\Http\Data\View;

/**
 * The client code. Note that it accepts the Abstract Factory class as the
 * parameter, which allows the client to work with any concrete factory type.
 */

class Page
{
    protected $path;
    protected $data;
    public function __construct(string $path = '', array $data = [])
    {
        $this->path = $path;
        $this->data = $data;
    }

    public function render(TemplateFactory $templateFactory)
    {
        $pageTemplate = $templateFactory->createPageTemplate($this->path);

        $renderer = $templateFactory->getRenderer();

        return $renderer->render($pageTemplate->getTemplateString(), $this->data);
    }
}

/**
 * Contoh penggunaan
 */
//$path = 'index';
//$data = [
//    'title' => 'hello world'
//];
//
//$view = new Page($path, $data);
//echo $view->render(new TwigTemplateFactory());
