<?php

namespace Spatie\ImageOptimizer\Optimizers;

use Spatie\ImageOptimizer\Image;

class Gifsicle extends BaseOptimizer
{
    public $binaryName = 'gifsicle';

    public function canHandle(Image $image): bool
    {
        return $image->mime() === 'image/gif';
    }
}
