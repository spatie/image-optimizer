<?php

namespace Spatie\ImageOptimizer\Optimizers;

use Spatie\ImageOptimizer\Image;

class Avifenc extends BaseOptimizer
{
    public $binaryName = 'avifenc';
    public $decodeBinaryName = 'avifdec';

    public function canHandle(Image $image): bool
    {
        return $image->mime() === 'image/avif' || $image->extension() === 'avif';
    }

    public function getCommand(): string
    {
        $this->tmpPath = tempnam(sys_get_temp_dir(), 'avifdec') . '.png';

        $decodeOptionString = implode(' ', [
            '-j all',
            '--ignore-icc',
            '--no-strict',
            '--png-compress 0',
        ]);
        $encodeOptionString = implode(' ', $this->options);

        $decode = "\"{$this->binaryPath}{$this->decodeBinaryName}\" {$decodeOptionString}"
            .' '.escapeshellarg($this->imagePath)
            .' '.escapeshellarg($this->tmpPath);

        $encode = "\"{$this->binaryPath}{$this->binaryName}\" {$encodeOptionString}"
            .' '.escapeshellarg($this->tmpPath)
            .' '.escapeshellarg($this->imagePath);

        return $decode . ' && ' . $encode;
    }
}
