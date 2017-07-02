<?php

namespace Spatie\ImageOptimizer\Optimizers;

class Jpegoptim extends BaseOptimizer
{
    public $binaryName = 'jpegoptim';

    public function __construct()
    {
        $this->setOptions([
            '--strip-all',
            '--all-progressive'
            ]);
    }

    public function canHandle(string $mimeType): bool
    {
        return $mimeType === "image/jpeg";
    }
}

