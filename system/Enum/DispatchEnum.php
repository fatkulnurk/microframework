<?php


namespace Fatkulnurk\Microframework\Enum;

use MyCLabs\Enum\Enum;

class DispatchEnum extends Enum
{
    private const NOT_FOUND = 0;
    private const FOUND = 1;
    private const METHOD_NOT_ALLOWED = 2;
}
