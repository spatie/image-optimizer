<?php

namespace Spatie\ImageOptimizer\Optimizers;

class Jpegoptim extends BaseOptimizer
{
    public $binaryName = 'jpegoptim';

    public function canHandle(string $mimeType): bool
    {
        return $mimeType === 'image/jpeg';
    }
}
