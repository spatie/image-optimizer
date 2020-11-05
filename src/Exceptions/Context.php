<?php

namespace Spatie\ImageOptimizer\Exceptions;

use Spatie\ImageOptimizer\OptimizerChain;
use Spatie\ImageOptimizer\SizeRestrictedImageOptimizer;

class Context
{
    /** @var string */
    private $image;

    /** @var SizeRestrictedImageOptimizer */
    private $optimizer;

    /** @var int */
    private $currentSize;

    /** @var OptimizerChain[] */
    private $optimizerChains;

    public function __construct(
        string $image,
        SizeRestrictedImageOptimizer $optimizer,
        int $currentSize,
        array $optimizerChains
    ) {
        $this->image = $image;
        $this->optimizer = $optimizer;
        $this->currentSize = $currentSize;
        $this->optimizerChains = $optimizerChains;
    }

    public function image(): string
    {
        return $this->image;
    }

    public function optimizer(): SizeRestrictedImageOptimizer
    {
        return $this->optimizer;
    }

    public function currentSize(): int
    {
        return $this->currentSize;
    }

    public function optimizerChains(): array
    {
        return $this->optimizerChains;
    }
}
