<?php

namespace Spatie\ImageOptimizer\Optimizers;

use Spatie\ImageOptimizer\Image;

class Cwebp extends BaseOptimizer
{
    public $binaryName = 'cwebp';

    public function canHandle(Image $image): bool
    {
        return in_array($image->mime(), [
            'image/png',
            'image/jpeg',
        ]);
    }

    public function getCommand(): string
    {
        $optionString = implode(' ', $this->options);

        return "\"{$this->binaryPath}{$this->binaryName}\" {$optionString}"
            .' '.escapeshellarg($this->imagePath)
            .' -o '.
            escapeshellarg(
                preg_replace(
                    '/'.pathinfo($this->imagePath, PATHINFO_EXTENSION).'$/',
                    'webp',
                    $this->imagePath
                )
            );
    }
}
