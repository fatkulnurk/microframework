<?php

declare(strict_types=1);

namespace Fatkulnurk\Microframework\Routing\DataGenerator;

use function implode;

class MarkBased extends RegexBasedAbstract
{
    /**
     * {@inheritdoc}
     */
    protected function getApproxChunkSize(): int
    {
        return 30;
    }

    /**
     * {@inheritdoc}
     */
    protected function processChunk(array $regexToRoutesMap): array
    {
        $routeMap = [];
        $regexes = [];
        $markName = 'a';

        foreach ($regexToRoutesMap as $regex => $route) {
            $regexes[] = $regex.'(*MARK:'.$markName.')';
            $routeMap[$markName] = [$route->handler, $route->variables];

            $markName++;
        }

        $regex = '~^(?|'.implode('|', $regexes).')$~';

        return ['regex' => $regex, 'routeMap' => $routeMap];
    }
}
