<?php

namespace Spatie\ImageOptimizer\Optimizers;

use Spatie\ImageOptimizer\Image;

class Cwebp extends BaseOptimizer
{
    public $binaryName = 'cwebp';

    public function canHandle(Image $image): bool
    {
        return $image->mime() === 'image/webp';
    }

    public function getCommand(): string
    {
        $optionString = implode(' ', $this->options);

        return "\"{$this->binaryPath}{$this->binaryName}\" {$optionString}"
            .' '.escapeshellarg($this->imagePath)
            .' -o '.escapeshellarg($this->imagePath);
    }

    public function getVersionCommand(): string
    {
        return "\"{$this->binaryPath}{$this->binaryName}\" -version";
    }
}
