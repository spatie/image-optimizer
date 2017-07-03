<?php

namespace Spatie\ImageOptimizer\Optimizers;

class Pngquant extends BaseOptimizer
{
    public $binaryName = 'pngquant';

    public function canHandle(string $mimeType): bool
    {
        return $mimeType === 'image/png';
    }

    public function getCommand()
    {
        $optionString = implode(' ', $this->options);

        return "{$this->binaryName} {$optionString} '{$this->imagePath}' --output='{$this->imagePath}'";
    }
}
