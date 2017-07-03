<?php

namespace Spatie\ImageOptimizer\Optimizers;

class Optipng extends BaseOptimizer
{
    public $binaryName = 'optipng';

    public function canHandle(string $mimeType): bool
    {
        return $mimeType === 'image/png';
    }
}
