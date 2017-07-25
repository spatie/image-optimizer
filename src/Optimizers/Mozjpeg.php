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
        // Directly overwriting of file is not possible, so do a mv after compression
        return "{$this->binaryName} {$optionString}"
            .' -outfile '.escapeshellarg($this->imagePath.'optimized.jpg')
            .' '.escapeshellarg($this->imagePath).' && mv '.escapeshellarg($this->imagePath.'optimized.jpg')
            .' '.escapeshellarg($this->imagePath);
    }
}
