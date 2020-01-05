<?php
namespace Fatkulnurk\Microframework\Http\Data\View;

interface PageTemplate
{
    public function getTemplateString();

    public function openFileTemplate();
}