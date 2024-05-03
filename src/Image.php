<?php

namespace Spatie\ImageOptimizer;

use InvalidArgumentException;

class Image
{
    protected $pathToImage = '';
    protected const ALLOWED_PROTOCOLS = ['file'];

    protected const WINDOWS_LOCAL_FILENAME_REGEX = '/^[a-z]:(?:[\\\\\/]?(?:[\w\s!#()-]+|[\.]{1,2})+)*[\\\\\/]?/i';

    public function __construct(string $pathToImage)
    {
        if (! $this->isProtocolAllowed($pathToImage)) {
            throw new InvalidArgumentException(\sprintf('The output file scheme is not supported. Expected one of [\'%s\'].', \implode('\', \'', self::ALLOWED_PROTOCOLS)));
        }

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

    protected function isProtocolAllowed($filename)
    {
        if (false === $parsedFilename = \parse_url($filename)) {
            throw new InvalidArgumentException('The filename is not valid.');
        }

        $protocol = isset($parsedFilename['scheme']) ? \mb_strtolower($parsedFilename['scheme']) : 'file';

        if (
            \PHP_OS_FAMILY === 'Windows'
            && \strlen($protocol) === 1
            && \preg_match(self::WINDOWS_LOCAL_FILENAME_REGEX, $filename)
        ) {
            $protocol = 'file';
        }

        return \in_array($protocol, self::ALLOWED_PROTOCOLS, true);
    }
}
