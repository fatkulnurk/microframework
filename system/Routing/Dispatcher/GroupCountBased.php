<?php

declare(strict_types=1);

namespace Fatkulnurk\Microframework\Routing\Dispatcher;

use function count;
use function preg_match;

class GroupCountBased extends RegexBasedAbstract
{
    /**
     * {@inheritdoc}
     */
    protected function dispatchVariableRoute(array $routeData, string $uri): array
    {
        foreach ($routeData as $data) {
            if (! preg_match($data['regex'], $uri, $matches)) {
                continue;
            }

            [$handler, $varNames] = $data['routeMap'][count($matches)];

            $vars = [];
            $i = 0;
            foreach ($varNames as $varName) {
                $vars[$varName] = $matches[++$i];
            }

            return [self::FOUND, $handler, $vars];
        }

        return [self::NOT_FOUND];
    }
}
