<?php

namespace Spatie\ImageOptimizer;

use Iterator;
use Spatie\ImageOptimizer\Exceptions\Context;
use Spatie\ImageOptimizer\Exceptions\UnableToGetRestrictedSizeException;
use UnexpectedValueException;

class SizeRestrictedImageOptimizer
{
    /** @var int */
    private $allowedSize;

    /** @var Iterator */
    private $optimizerChains;

    public function __construct(int $allowedSize, Iterator $optimizerChains)
    {
        if ($allowedSize <= 0) {
            throw new UnexpectedValueException(sprintf('Allowed size of %d is not acceptable',
                $allowedSize));
        }
        $this->allowedSize = $allowedSize;
        $this->optimizerChains = $optimizerChains;
    }

    /**
     * @param string $image path to image on the server
     * @param bool $strict default is false, on true if unable to shrink image to target size
     * @throws UnableToGetRestrictedSizeException
     */
    public function optimize(string $image, bool $strict = false): void
    {
        $usedOptimizerChains = [];
        while (filesize($image) >= $this->allowedSize) {
            $this->optimizerChains->next();
            $currentChain = $this->optimizerChains->current();
            if (! $currentChain) {
                break;
            }
            $usedOptimizerChains[] = $currentChain;
            $currentChain->optimize($image);
        }

        if ($strict && ($currentSize = filesize($image)) > $this->allowedSize) {
            throw new UnableToGetRestrictedSizeException(sprintf('Unable to optimize image %s to size %d. Current image size is %d',
                $image, $this->allowedSize, $currentSize),
                0, null,
                new Context($image, $this, $currentSize, $usedOptimizerChains));
        }
    }

    public function allowedSize(): int
    {
        return $this->allowedSize;
    }
}
