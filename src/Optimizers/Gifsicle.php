<?php

namespace Spatie\ImageOptimizer\Optimizers;

class Gifsicle extends BaseOptimizer
{
    public $binaryName = 'gifsicle';

    public function canHandle(string $mimeType): bool
    {
        return $mimeType === 'image/gif';
    }
}
