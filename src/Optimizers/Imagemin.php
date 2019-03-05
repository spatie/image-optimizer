<?php

namespace Spatie\ImageOptimizer\Optimizers;

use Spatie\ImageOptimizer\Image;

class Imagemin extends BaseOptimizer
{
    public $binaryName = 'imagemin';

    public function canHandle(Image $image): bool
    {
        return $image->mime() === 'image/webp';
    }

    public function getCommand(): string
    {
        $optionString = implode(' ', $this->options);

        return "\"{$this->binaryPath}{$this->binaryName}\" {$optionString}"
            . ' --plugin=webp '
            . ' ' . escapeshellarg($this->imagePath)
            . ' --out-dir=' . escapeshellarg($this->imagePath);
    }
}
