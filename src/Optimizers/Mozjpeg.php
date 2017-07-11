<?php

namespace Spatie\ImageOptimizer\Optimizers;

use Spatie\ImageOptimizer\Image;

class Mozjpeg extends BaseOptimizer
{
    public $binaryName = 'mozjpeg';

    public function canHandle(Image $image): bool
    {
        return $image->mime() === 'image/jpeg';
    }

    public function getCommand(): string
    {
        $optionString = implode(' ', $this->options);

        return "{$this->binaryName} {$optionString}"
            .' -copy none -outfile '.escapeshellarg($this->imagePath)
            .' '.escapeshellarg($this->imagePath);
    }
}
