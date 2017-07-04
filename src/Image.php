<?php

namespace Spatie\ImageOptimizer;

use InvalidArgumentException;

class Image
{
    protected $pathToImage = '';

    public function __construct(string $pathToImage)
    {
        if (! file_exists($pathToImage)) {
            throw new InvalidArgumentException("`{$pathToImage}` does not exist");
        }

        $this->pathToImage = $pathToImage;
    }

    public function mime(): string
    {
        return mime_content_type($this->pathToImage);
    }

    public function path(): string
    {
        return $this->pathToImage;
    }

    public function extension(): string
    {
        $extension = pathinfo($this->pathToImage, PATHINFO_EXTENSION);

        return strtolower($extension);
    }
}
