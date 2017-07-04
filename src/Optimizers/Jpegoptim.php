<?php

namespace Spatie\ImageOptimizer\Optimizers;

use Spatie\ImageOptimizer\Image;

class Jpegoptim extends BaseOptimizer
{
    public $binaryName = 'jpegoptim';

    public function canHandle(Image $image): bool
    {
        return $image->mime() === 'image/jpeg';
    }
}
