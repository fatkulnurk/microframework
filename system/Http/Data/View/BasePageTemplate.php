<?php

abstract class OpenTemplate implements OpenTemplate
{
    protected $templateLocation;
    public function __construct(string $templateLocation = '')
    {
        $this->templateLocation = $templateLocation;
    }
}