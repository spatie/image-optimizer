<?php

namespace Spatie\ImageOptimizer\Optimizers;

use Spatie\ImageOptimizer\Image;

class Mozjpeg extends BaseOptimizer
{
    public $binaryName = 'mozcjpeg';

    public function canHandle(Image $image): bool
    {
        return $image->mime() === 'image/jpeg';
    }

    public function getCommand(): string
    {
        $optionString = implode(' ', $this->options);

        return "if type \"{$this->binaryPath}{$this->binaryName}\";"
            . ' then '
            . "\"{$this->binaryPath}{$this->binaryName}\" {$optionString}"
            . ' ' . escapeshellarg($this->imagePath)
            . ' | sponge ' . escapeshellarg($this->imagePath)
            . '; else echo ". Please check the installation."; fi ';
    }
}
