<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/app',
        __DIR__ . '/config',
        __DIR__ . '/test',
    ])
    ->withPhpSets(php83: true)
    ->withTypeCoverageLevel(0);
