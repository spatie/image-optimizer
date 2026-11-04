<?php

namespace Spatie\ImageOptimizer\Optimizers;

use RuntimeException;
use Spatie\ImageOptimizer\Image;

class Avifenc extends BaseOptimizer
{
    public $binaryName = 'avifenc';
    public $decodeBinaryName = 'avifdec';

    public function canHandle(Image $image): bool
    {
        if (version_compare(PHP_VERSION, '8.1.0', '<')) {
            return $image->extension() === 'avif';
        }

        return $image->mime() === 'image/avif';
    }

    public function getCommand(): string
    {
        return $this->getDecodeCommand().' && '
            .$this->getEncodeCommand();
    }

    protected function getDecodeCommand()
    {
        $tmpPath = tempnam(sys_get_temp_dir(), 'avifdec');

        if ($tmpPath === false) {
            throw new RuntimeException('Cannot create temporary file for avifdec.');
        }

        $this->tmpPath = $tmpPath;

        if (! rename($tmpPath, $tmpPath.'.png')) {
            throw new RuntimeException('Cannot rename temporary file for avifdec.');
        }

        $this->tmpPath = $tmpPath.'.png';

        $optionString = implode(' ', [
            '-j all',
            '--ignore-icc',
            '--no-strict',
            '--png-compress 0',
        ]);

        return "\"{$this->binaryPath}{$this->decodeBinaryName}\" {$optionString}"
            .' '.escapeshellarg($this->imagePath)
            .' '.escapeshellarg($this->tmpPath);
    }

    protected function getEncodeCommand()
    {
        $optionString = implode(' ', $this->options);

        return "\"{$this->binaryPath}{$this->binaryName}\" {$optionString}"
            .' '.escapeshellarg($this->tmpPath)
            .' '.escapeshellarg($this->imagePath);
    }
}
