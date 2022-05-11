<?php

uses(\Spatie\ImageOptimizer\Test\TestCase::class)->in(__DIR__);

function getTempFilePath(string $fileName): string
{
    $source = __DIR__ . "/testfiles/{$fileName}";

    $destination = __DIR__ . "/temp/{$fileName}";

    copy($source, $destination);

    return $destination;
}

function getTestFilePath(string $fileName): string
{
    return __DIR__ . "/testfiles/{$fileName}";
}
