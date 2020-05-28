<?php

use Fatkulnurk\Microframework\Http\Data\Document\DocumentArray;
use Fatkulnurk\Microframework\Http\Data\Document\DocumentFactory;
use Fatkulnurk\Microframework\Http\Data\Document\DocumentObject;
use Fatkulnurk\Microframework\Http\Data\Document\JsonFactory;
use Fatkulnurk\Microframework\Http\Data\Document\XmlFactory;
use Fatkulnurk\Microframework\Http\Data\View\LexTemplateFactory;
use Fatkulnurk\Microframework\Http\Data\View\PageTemplate;
use Fatkulnurk\Microframework\Http\Data\View\TemplateFactory;
use Fatkulnurk\Microframework\Http\Data\View\TemplateRenderer;
use Fatkulnurk\Microframework\Http\Data\View\TwigTemplateFactory;
use PHPUnit\Framework\TestCase;

class AbstractFactoryTest extends TestCase
{
    public function provideDocumentFactory()
    {
        return [
            [new JsonFactory()],
            [new XmlFactory()]
        ];
    }

    /**
     * @dataProvider provideDocumentFactory
     * @param DocumentFactory $documentFactory
     **/
    public function testCanCreateDocumentJsonXmlFromObjectArray(DocumentFactory $documentFactory)
    {
        $dataObj = (object) ['message' => 'testing'];
        $dataArr = (array) ['message' => 'testing'];
        $this->assertInstanceOf(DocumentObject::class, $documentFactory->createFromObject($dataObj));
        $this->assertInstanceOf(DocumentArray::class, $documentFactory->createFromArray($dataArr));
    }


    public function provideViewFactory()
    {
        return [
            [new LexTemplateFactory()],
            [new TwigTemplateFactory()]
        ];
    }

    /**
     * @dataProvider provideViewFactory
     * @param TemplateFactory $templateFactory
     **/
    public function testCanCreateViewFromPageAndRender(TemplateFactory $templateFactory)
    {
        $dataObj = (object) ['message' => 'testing'];
        $dataArr = (array) ['message' => 'testing'];
        $this->assertInstanceOf(PageTemplate::class, $templateFactory->createPageTemplate());
        $this->assertInstanceOf(TemplateRenderer::class, $templateFactory->getRenderer());
    }
}
