<?php

namespace Spatie\ImageOptimizer\Optimizers;

class Optipng extends BaseOptimizer
{
    public $binaryName = 'optipng';

    public function __construct()
    {
        $this->setOptions([
            '-i0',
            '-o2',
            '-quiet'
        ]);
    }

    public function canHandle(string $mimeType): bool
    {
        return $mimeType === 'image/png';
    }
}
