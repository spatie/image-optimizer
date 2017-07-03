<?php

namespace Spatie\ImageOptimizer\Optimizers;

class Jpegtran extends BaseOptimizer
{
    public $binaryName = 'jpegtran';

    public function canHandle(string $mimeType): bool
    {
        return $mimeType === 'image/jpeg';
    }
}
